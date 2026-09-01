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

return [
    'ctrl' => [
        'hideTable' => true,
        'enablecolumns' => [
        ],
    ],
    'columns' => [
        // Kept as a plain int (not `type: language`) — Extbase treats
        // `type: language` as a relation-ish column and would write '' into
        // the NOT NULL int column at insert time.
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
        // The Index model exposes `$indexer` as an ObjectStorage<Indexer>
        // (m:n), but the actual DB column `indexer int(11)` stores the scalar
        // uid of the owning Indexer record (populated by the indexer code
        // via `setAdditionalFields`). Declared here as a plain int so
        // Extbase writes an integer, not an empty select-value.
        'indexer' => [
            'label' => 'indexer',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'int',
                'default' => 0,
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
        'title' => [
            'label' => 'title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'content' => [
            'label' => 'content',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'abstract' => [
            'label' => 'abstract',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 5,
                'eval' => 'trim',
            ],
        ],
        'params' => [
            'label' => 'params',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 3,
                'eval' => 'trim',
            ],
        ],
        'target' => [
            'label' => 'target',
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
        'datetime' => [
            'label' => 'datetime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
            ],
        ],
        'categories' => [
            'label' => 'categories',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'ranking' => [
            'label' => 'ranking',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'int',
                'default' => 0,
            ],
        ],
        // NB: the Index model also declares `$hits` and `$filereference` but
        // ext_tables.sql doesn't create either column, so they must NOT be
        // in TCA — Extbase would otherwise try to write to a non-existent
        // column at persist time ("Unknown column 'filereference' in 'field
        // list'").
    ],
];
