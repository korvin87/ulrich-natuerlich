<?php
/*
 * abavo_form
 * 
 * @copyright   2016 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'abavo Form',
    'description' => 'Formular Extension für multifunktionale Einsätze',
    'category' => 'plugin',
    'version' => '1.1.1',
    'state' => 'stable',
    'author' => 'Mathias Bruckmoser',
    'author_email' => 'dev@abavo.de',
    'author_company' => 'abavo GmbH',
	'state' => 'stable',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-8.7.99',
            'static_info_tables' => '6.3.0-6.5.99',
            'php' => '7.0.0-7.0.99'
        ]
    ]
];
