<?php

return [
    \Abavo\AbavoSearch\Domain\Model\Ttcontent::class => [
        'tableName' => 'tt_content',
        // Column names on tt_content that don't follow Extbase's camelCase→
        // snake_case auto-convention. `contentType` is a virtual query field
        // used by TtcontentRepository — it maps to the `CType` column.
        'properties' => [
            'contentType' => ['fieldName' => 'CType'],
            'colPos'      => ['fieldName' => 'colPos'],
        ],
    ],
    // Historically mapped via ext_typoscript_setup.txt (v9-era format that TYPO3
    // v10+ ignores). Without this entry Extbase falls back to the auto-derived
    // name tx_abavosearch_domain_model_page, which does not exist — breaking
    // abavo_search:update at runtime.
    \Abavo\AbavoSearch\Domain\Model\Page::class => [
        'tableName' => 'pages',
    ],
    \Abavo\AbavoSearch\Domain\Model\Index::class => [
        'tableName' => 'tx_abavosearch_domain_model_index',
        'properties' => [
            'title'          => ['fieldName' => 'title'],
            'content'        => ['fieldName' => 'content'],
            'params'         => ['fieldName' => 'params'],
            'target'         => ['fieldName' => 'target'],
            'refid'          => ['fieldName' => 'refid'],
            'abstract'       => ['fieldName' => 'abstract'],
            'fegroup'        => ['fieldName' => 'fegroup'],
            'datetime'       => ['fieldName' => 'datetime'],
            'sysLanguageUid' => ['fieldName' => 'sys_language_uid'],
            'categories'     => ['fieldName' => 'categories'],
            'ranking'        => ['fieldName' => 'ranking'],
            'hits'           => ['fieldName' => 'hits'],
        ],
    ],
];
