<?php
/**
 * ElaticSearch histogram facet model.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Smile Searchandising Suite to newer
 * versions in the future.
 *
 * This work is a fork of Johann Reinke <johann@bubblecode.net> previous module
 * available at https://github.com/jreinke/magento-elasticsearch
 *
 * @category  Smile
 * @package   Smile_ElasticSearch
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2013 Smile
 * @license   Apache License Version 2.0
 */
class Smile_ElasticSearch_Model_Resource_Engine_Elasticsearch_Query_Agg_QueryGroup
    extends Smile_ElasticSearch_Model_Resource_Engine_Elasticsearch_Query_Agg_Abstract
{
    /**
     * Default Options.
     *
     * @var array
     */
    protected $_options = [
        'queries' => [],
        'prefix'  => 'group_'
    ];

    /**
     * Transform the facet into an ES syntax compliant array.
     *
     * @return array
     */
    protected function _getAggQuery()
    {
        $queries = [];
        $prefix = $this->getPrefix();
        foreach ($this->_options['queries'] as $facetName => $query) {
            $realName = $prefix . $facetName;
            $queries[$realName] = [
                'filter' => ['query' => ['query_string' => ['query' =>  $query]]]
            ];
        }

        return $queries;
    }

    /**
     * Set the facet as group of facet
     *
     * @return bool
     */
    public function isGroup()
    {
        return true;
    }

    /**
     * Get facet prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->_options['prefix'];
    }

    /**
     * Parse the response to extract facet items.
     *
     * @param array $response Query response data.
     *
     * @return array
     */
    public function getItems($response = null)
    {
        $result = array();

        if ($response == null && $this->_response) {
            $response = $this->_response;
        }

        $prefix = $this->getPrefix();
        foreach ($response as $facetName => $facet) {
            if (strpos($facetName, $prefix) === 0) {
                $key = str_replace($prefix, '', $facetName);
                $result[$key] = $facet['doc_count'];
            }
        }
        return $result;
    }
}