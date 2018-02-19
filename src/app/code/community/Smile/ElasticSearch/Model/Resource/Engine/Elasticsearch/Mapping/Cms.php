<?php
/**
 * CMS Pages indexing type
 *
 * @package   Smile
 * @copyright 2013 Smile
 * @license   Apache License Version 2.0
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @author    Pierre Bernard <pierre.bernard@foodspring.com>
 */

class Smile_ElasticSearch_Model_Resource_Engine_Elasticsearch_Mapping_Cms
    extends Smile_ElasticSearch_Model_Resource_Engine_Elasticsearch_Mapping_Abstract
{
    public function __construct()
    {
        parent::__construct();

        $this->_type = 'cms';
    }

    /**
     * Return a list of all searchable field for the current type (by locale code).
     *
     * @param string $languageCode Language code.
     * @param string $searchType Type of search currentlty used.
     * @param string $analyzer Allow to force the analyzer used for the field (whitespace, ...).
     *
     * @return array.
     */
    public function getSearchFields($languageCode, $searchType = null, $analyzer = null)
    {
        if (!isset($this->_searchFields[$searchType . $analyzer])) {
            $defaultSearchField = $this->_getDefaultSearchFieldBySearchType($languageCode, $searchType);
            $this->_searchFields[$searchType . $analyzer][] = $defaultSearchField;
        }

        return $this->_searchFields[$searchType . $analyzer];
    }

    /**
     * Rebuild the index (full or diff).
     *
     * @param int|null   $storeId Store id the index should be rebuilt for. If null, all store id will be rebuilt.
     * @param array|null $ids Ids the index should be rebuilt for. If null, processing a fulll reindex
     *
     * @return Smile_ElasticSearch_Model_Resource_Engine_Elasticsearch_Mapping_Abstract
     */
    public function rebuildIndex($storeId = null, $ids = null)
    {
        if (is_null($storeId)) {
            $storeIds = array_keys($this->_stores);
            foreach ($storeIds as $storeId) {
                $this->_rebuildStoreIndex((int)$storeId);
            }
        } else {
            $this->_rebuildStoreIndex((int)$storeId);
        }

        $this->getCurrentIndex()->refresh();

        return $this;
    }

    /**
     * Get mapping properties as stored into the index
     *
     * @return array
     */
    protected function _getMappingProperties()
    {
        $mapping = parent::_getMappingProperties();

        $mapping['properties'] = array_merge($mapping['properties'], $this->_getSpellingFieldMapping());

        foreach ($this->_stores as $store) {
            $languageCode = $this->_helper->getLanguageCodeByStore($store);
            foreach ($this->_getFieldToIndex() as $fieldToIndex) {
                $fieldMapping = $this->_getStringMapping(
                    $fieldToIndex . '_' . $languageCode, $languageCode, 'string', true, true, true
                );
                $mapping['properties'] = array_merge($mapping['properties'], $fieldMapping);
            }
        }

        $mapping['properties']['published_date'] = ['type' => 'date'];
        $mapping['properties']['unique'] = ['type' => 'string', 'store' => false, 'index' => 'not_analyzed'];

        return $mapping;
    }

    /**
     * Load activated CMS pages
     *
     * @param integer $storeId
     *
     * @return void
     */
    protected function _rebuildStoreIndex($storeId)
    {
        $pages = Mage::getResourceModel('cms/page_collection')
            ->addStoreFilter($storeId)
            ->addFieldToFilter('is_active', 1);

        $languageCode = $this->_helper->getLanguageCodeByStore(Mage::app()->getStore($storeId));
        $docs = [];

        foreach ($pages as $page) {
            $data = $this->_getData($page, $languageCode);
            $data['unique'] = $page->getId() . '|' . $storeId;
            $docs[] = $data;
        }

        if (!empty($docs)) {
            Mage::helper('catalogsearch')->getEngine()->saveEntityIndexes($storeId, $docs, $this->_type);
        }
    }

    /**
     * Get prepare data
     *
     * @param Mage_Cms_Model_Page $page
     * @param string              $languageCode
     *
     * @return array
     */
    protected function _getData($page, $languageCode)
    {
        $data = [];
        foreach ($this->_getFieldToIndex() as $fieldToIndex) {
            $data[$fieldToIndex . '_' . $languageCode] = $page->getData($fieldToIndex);
        }

        return $data;
    }

    /**
     * Return an array of all fields to index for an entity
     *
     * @return array
     */
    protected function _getFieldToIndex()
    {
        return [
            'title',
            'content',
            'meta_keyword',
            'mety_description'
        ];
    }
}