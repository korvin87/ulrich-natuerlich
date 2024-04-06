<?php
/**
 * abavo_search - sys_file.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 29.06.2018 - 08:52:31
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */
defined('TYPO3') or die();

$temporaryColumns = [
    'tx_abavosearch_indexer' => [
        'exclude' => 0,
        'label' => 'EXT:abavo_search FAL-Indexer',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'foreign_table' => 'tx_abavosearch_domain_model_indexer',
            'foreign_table_where' => ' AND tx_abavosearch_domain_model_indexer.type="Fal"',
            'size' => 1,
            'maxitems' => 1
        ]
    ],
    'tx_abavosearch_index_tstamp' => [
        'exclude' => 0,
        'label' => 'EXT:abavo_search Index Timestamp',
        'config' => [
            'type' => 'none',
            'format' => 'datetime',
        ]
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'sys_file_metadata', $temporaryColumns
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'sys_file_metadata', 'tx_abavosearch_indexer, tx_abavosearch_index_tstamp'
);
