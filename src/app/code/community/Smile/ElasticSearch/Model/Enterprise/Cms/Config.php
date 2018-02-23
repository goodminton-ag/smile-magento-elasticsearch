<?php
/**
 * Add searchable flag column to CMS page revision attribute
 *
 * @package   Smile_ElasticSearch
 * @copyright 2017 foodspring GmbH <https://www.foodspring.com>
 * @author    Pierre Bernard <pierre.bernard@foodspring.com>
 */

class Smile_ElasticSearch_Model_Enterprise_Cms_Config
    extends Enterprise_Cms_Model_Config
{
    public function __construct()
    {
        $this->_revisionControlledAttributes['page'][] = 'smile_is_searchable';
    }
}