<?php
/*
 * abavo_search
 *
 * @copyright   2018 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

/**
 * @author mbruckmoser
 */
if (!defined('TYPO3')) {
    die('Access denied.');
}

// This table is hidden from the backend (`hideTable = true`). But Extbase's
// DataMapFactory (v10+) builds its column map by iterating the TCA `columns`
// block, so a table with no columns declared cannot be persisted through the
// ORM: INSERTs skip every field except pid/crdate/tstamp. The block below
// re-declares each column that Extbase needs to write. `sys_language_uid` is
// kept as a plain int (not `type: language`) so Extbase writes an integer, not
// an empty-string relation placeholder into the NOT NULL int column.
return [
    'ctrl' => [
        'hideTable' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'enablecolumns' => [
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
                'default' => 0,
            ],
        ],
        'pid' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'refid' => [
            'label' => 'refid',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'search' => [
            'label' => 'search',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'fegroup' => [
            'label' => 'fegroup',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => '0',
            ],
        ],
    ],
];
