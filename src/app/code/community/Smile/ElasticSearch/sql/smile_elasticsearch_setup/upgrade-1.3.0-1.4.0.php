<?php
/**
 * Add searchable flag column to CMS page
 *
 * @package   Smile_ElasticSearch
 * @copyright 2017 foodspring GmbH <https://www.foodspring.com>
 * @author    Pierre Bernard <pierre.bernard@foodspring.com>
 */

/* @var Mage_Catalog_Model_Resource_Setup $this */

$this->startSetup();

$table = $this->getTable('cms/page');
if (!$this->getConnection()->tableColumnExists($table, 'smile_is_searchable')) {
    $this->getConnection()->addColumn(
        $table,
        'smile_is_searchable',
        [
            'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
            'default' => 0,
            'comment' => 'Is page searchable'
        ]
    );
}

$this->endSetup();