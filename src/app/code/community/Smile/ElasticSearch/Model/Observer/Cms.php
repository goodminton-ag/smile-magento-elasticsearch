<?php
/**
 * Add searchable flag field to CMS meta data page
 *
 * @package   Smile_ElasticSearch
 * @copyright 2017 foodspring GmbH <https://www.foodspring.com>
 * @author    Pierre Bernard <pierre.bernard@foodspring.com>
 */

class Smile_ElasticSearch_Model_Observer_Cms
{
    /**
     * Add new form field to meta data tab
     *
     * @param Varien_Event_Observer $observer
     *
     * @return void
     */
    public function adminhtmlCmsPageEditTabMetaPrepareForm(Varien_Event_Observer $observer)
    {
        /* @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $observer->getData('form')->getElement('meta_fieldset');
        $fieldset->addField(
            'smile_is_searchable',
            'select',
            [
                'name' => 'smile_is_searchable',
                'label' => Mage::helper('smile_elasticsearch')->__('Searchable'),
                'title' => Mage::helper('smile_elasticsearch')->__('Searchable'),
                'options' => Mage::getModel('adminhtml/system_config_source_yesno')->toArray()
            ]
        );
    }
}