<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title,description,appearance,cas_number,eg_number,granulation,bestbefor,qualities,media,origin_country,origin_countries,related_products,contact,categories,nextday,accordiontext_plant, accordiontext_origin, accordiontext_production, accordiontext_application text, accordiontext_facts',
        'iconfile' => 'EXT:ulrich_products/Resources/Public/Icons/tx_ulrichproducts_domain_model_product.png'
    ],
    'types' => [
        '1' => ['showitem' => '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.visibility;visibility, --palette--;;titlerow, slug, description, media, --div--;Akkordeon,--palette--;;productAccordion, --div--;Produktdetails,--palette--;;productDetails, --div--;Zuordnung, categories, branch, origin_countries, related_products, contacts'],
    ],
    'palettes' => [
        'visibility' => ['showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource;;1, starttime, endtime, hidden', 'canNotCollapse' => 1],
        'titlerow' => ['showitem' => 'title, nextday', 'canNotCollapse' => 1],
        'productDetails' => ['showitem' => 'appearance, cas_number, --linebreak--, eg_number, granulation, --linebreak--, bestbefor, qualities, --linebreak--, spec, physical_state, --linebreak--, chemical_properties, molecular_formula, --linebreak--, chemical_name, registration, --linebreak--,  e_number, grass_state, --linebreak--, container, inci, --linebreak--, einecs, melting_point, --linebreak--, durability, storage',
            'canNotCollapse' => 1],
        'productAccordion' => ['showitem' => 'accordiontext_plant, --linebreak--, accordiontext_plant_media, --linebreak--, accordiontext_origin, --linebreak--, accordiontext_origin_media, --linebreak--, accordiontext_production, --linebreak--, accordiontext_production_media, --linebreak--, accordiontext_application, --linebreak--, accordiontext_application_media, --linebreak--, accordiontext_facts, --linebreak--, accordiontext_facts_media',
            'canNotCollapse' => 1],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => ['type' => 'language'],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_ulrichproducts_domain_model_product',
                'foreign_table_where' => 'AND tx_ulrichproducts_domain_model_product.pid=###CURRENT_PID### AND tx_ulrichproducts_domain_model_product.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'behaviour' => [
                'allowLanguageSynchronization' => true
            ],
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'behaviour' => [
                'allowLanguageSynchronization' => true
            ],
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
            ],
        ],
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.description',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default',
                'fieldControl' => [
                    'fullScreenRichtext' => [
                        'disabled' => false,
                    ],
                ],
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'accordiontext_plant' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.accordiontext_plant',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default',
                'fieldControl' => [
                    'fullScreenRichtext' => [
                        'disabled' => false,
                    ],
                ],
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'accordiontext_plant_media' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.accordiontext_plant_media',
            'config' =>
                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'accordiontext_plant_media',
                    [
                        'appearance' => [
                            'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
                        ],
                        'maxitems' => 9999,
                        'overrideChildTca' => ['types' => [
                            '0' => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ]
                        ]]
                    ]
                ),
        ],
        'accordiontext_origin' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.accordiontext_origin',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default',
                'fieldControl' => [
                    'fullScreenRichtext' => [
                        'disabled' => false,
                    ],
                ],
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'accordiontext_origin_media' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.accordiontext_origin_media',
            'config' =>
                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'accordiontext_origin_media',
                    [
                        'appearance' => [
                            'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
                        ],
                        'maxitems' => 9999,
                        'overrideChildTca' => ['types' => [
                            '0' => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ]
                        ]]
                    ]
                ),
        ],
        'accordiontext_production' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.accordiontext_production',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default',
                'fieldControl' => [
                    'fullScreenRichtext' => [
                        'disabled' => false,
                    ],
                ],
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'accordiontext_production_media' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.accordiontext_production_media',
            'config' =>
                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'accordiontext_production_media',
                    [
                        'appearance' => [
                            'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
                        ],
                        'maxitems' => 9999,
                        'overrideChildTca' => ['types' => [
                            '0' => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ]
                        ]]
                    ]
                ),
        ],
        'accordiontext_application' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.accordiontext_application',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default',
                'fieldControl' => [
                    'fullScreenRichtext' => [
                        'disabled' => false,
                    ],
                ],
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'accordiontext_application_media' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.accordiontext_application_media',
            'config' =>
                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'accordiontext_application_media',
                    [
                        'appearance' => [
                            'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
                        ],
                        'maxitems' => 9999,
                        'overrideChildTca' => ['types' => [
                            '0' => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ]
                        ]]
                    ]
                ),
        ],
        'accordiontext_facts' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.accordiontext_facts',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default',
                'fieldControl' => [
                    'fullScreenRichtext' => [
                        'disabled' => false,
                    ],
                ],
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'accordiontext_facts_media' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.accordiontext_facts_media',
            'config' =>
                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'accordiontext_facts_media',
                    [
                        'appearance' => [
                            'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
                        ],
                        'maxitems' => 9999,
                        'overrideChildTca' => ['types' => [
                            '0' => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ]
                        ]]
                    ]
                ),
        ],
        'appearance' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.appearance',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'cas_number' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.cas_number',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'eg_number' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.eg_number',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'granulation' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.granulation',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'bestbefor' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.bestbefor',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'qualities' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.qualities',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'spec' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.spec',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'physical_state' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.physical_state',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'chemical_properties' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.chemical_properties',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'molecular_formula' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.molecular_formula',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'chemical_name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.chemical_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'registration' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.registration',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'e_number' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.e_number',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'grass_state' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.grass_state',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'container' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.container',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'inci' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.inci',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'einecs' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.einecs',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'melting_point' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.melting_point',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'durability' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.durability',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'storage' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.storage',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'media' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.media',
            'config' =>
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'media',
                [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
                ],
                'maxitems' => 9999,
                'overrideChildTca' => ['types' => [
                    '0' => [
                        'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                    ],
                    \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                        'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                    ],
                    \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                        'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                    ],
                    \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                        'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                    ],
                    \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                        'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                    ],
                    \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                        'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                    ]
                ]]
                ]
            ),
        ],
        'origin_countries' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'Herkunftsländer',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'static_countries',
                'foreign_table_where' => ' ORDER BY cn_short_de',
                'MM' => 'tx_ulrichproducts_product_static_countries_mm',
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 5,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],
        ],
        'related_products' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.related_products',
            'config' => [
                'type' => 'select',
                #'internal_type' => 'db',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_ulrichproducts_domain_model_product',
                'foreign_table_where' => ' AND tx_ulrichproducts_domain_model_product.uid!=###THIS_UID### AND tx_ulrichproducts_domain_model_product.l10n_parent=0 ORDER BY tx_ulrichproducts_domain_model_product.title',
                'MM' => 'tx_ulrichproducts_product_product_mm',
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 9999,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],
        ],
        'contacts' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.contact',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_ulrichproducts_domain_model_contact',
                'foreign_table_where' => ' ORDER BY name',
                'MM' => 'tx_ulrichproducts_product_contact_mm',
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 5,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],
        ],
        'categories' => [
            'exclude' => true,
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.categories',
            'config' => [
                'type' => 'select',
                'renderType' => '',
                'foreign_table' => 'sys_category',
                'MM' => 'tx_ulrichproducts_product_category_mm',
            ],
        ],
        'branch' => [
            'exclude' => true,
            'label' => 'Branche',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectCheckBox',
                'foreign_table' => 'pages',
                'foreign_table_where' => 'pages.pid=16 ORDER BY pages.sorting',
                'MM' => 'tx_ulrichproducts_product_pages_mm',
            ],
        ],
        'nextday' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrichproducts_domain_model_product.nextday',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],
        'slug' => [
            'exclude' => true,
            'config' => [
                'type' => 'slug',
                'generatorOptions' => [
                    'fields' => ['title'],
                    'fieldSeparator' => '/',
                    'prefixParentPageSlug' => true,
                    'replacements' => [
                        '/' => '',
                    ],
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite',
                'default' => ''
            ],
        ]
    ],
];
