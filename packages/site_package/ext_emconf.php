<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'SitePackage',
    'description' => '',
    'category' => 'templates',
    'version' => '1.0.0',
    'state' => 'stable',
    'author' => 'Mathias Bruckmoser, Simon Würstle',
    'author_email' => 'dev@abavo.de',
    'author_company' => 'abavo GmbH',
	'state' => 'excludeFromUpdates',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.7.99'
        ]
    ]
];
