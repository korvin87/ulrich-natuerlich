
<?php

return [
    \Abavo\AbavoSearch\Domain\Model\Ttcontent::class => [
        'tableName' => 'tt_content'
    ],
    \Abavo\AbavoSearch\Domain\Model\Index::class => [
        'tableName' => 'tx_abavosearch_domain_model_index',
        'properties' => [
            'indexer' => [
                'fieldName' => 'indexer'
            ],
            'title' => [
                'fieldName' => 'title'
            ]            
        ]
    ]
];