<?php
return [
    'BE' => [
        'compressionLevel' => 0,
        'debug' => false,
        'explicitADmode' => 'explicitAllow',
        'installToolPassword' => '$pbkdf2-sha256$25000$Sb/59eXCg5T7q2ly7DreoQ$uvmx5883C9VNkslBlf1JUMcPWWA4VOLdP/U4CgDAEJ0',
        'loginSecurityLevel' => 'normal',
    ],
    'DB' => [
        'Connections' => [
            'Default' => [
                'charset' => 'utf8',
                'dbname' => '',
                'driver' => 'mysqli',
                'host' => '',
                'password' => '',
                'port' => 3306,
                'user' => '',
            ],
        ],
    ],
    'EXT' => [],
    'EXTCONF' => [
        'helhum-typo3-console' => [
            'initialUpgradeDone' => '8.7',
        ],
        'lang' => [
            'availableLanguages' => [
                'de',
                'fr',
            ],
        ],
    ],
    'EXTENSIONS' => [
        'backend' => [
            'backendFavicon' => '',
            'backendLogo' => '',
            'loginBackgroundImage' => '',
            'loginFootnote' => '',
            'loginHighlightColor' => '',
            'loginLogo' => '',
            'loginLogoAlt' => '',
        ],
        'environment' => [
            'fallbackContext' => '',
            'forceContext' => '',
        ],
        'extensionmanager' => [
            'automaticInstallation' => '1',
            'offlineMode' => '0',
        ],
        'gridelements' => [
            'additionalStylesheet' => '',
            'disableAutomaticUnusedColumnCorrection' => '0',
            'disableCopyFromPageButton' => '0',
            'disableDragInWizard' => '0',
            'fluidBasedPageModule' => '0',
            'nestingInListModule' => '0',
            'overlayShortcutTranslation' => '0',
        ],
        'indexed_search' => [
            'catdoc' => '/usr/bin/',
            'debugMode' => '0',
            'deleteFromIndexAfterEditing' => '1',
            'disableFrontendIndexing' => '0',
            'enableMetaphoneSearch' => '1',
            'flagBitMask' => '192',
            'fullTextDataLength' => '0',
            'ignoreExtensions' => '',
            'indexExternalURLs' => '0',
            'maxAge' => '0',
            'maxExternalFiles' => '5',
            'minAge' => '24',
            'pdf_mode' => '20',
            'pdftools' => '/usr/bin/',
            'ppthtml' => '/usr/bin/',
            'unrtf' => '/usr/bin/',
            'unzip' => '/usr/bin/',
            'useCrawlerForExternalFiles' => '0',
            'useMysqlFulltext' => '0',
            'xlhtml' => '/usr/bin/',
        ],
        'news' => [
            'advancedMediaPreview' => '1',
            'archiveDate' => 'date',
            'categoryBeGroupTceFormsRestriction' => '0',
            'categoryRestriction' => '',
            'contentElementRelation' => '1',
            'dateTimeNotRequired' => '0',
            'hidePageTreeForAdministrationModule' => '0',
            'manualSorting' => '0',
            'prependAtCopy' => '1',
            'resourceFolderImporter' => '/news_import',
            'rteForTeaser' => '0',
            'showAdministrationModule' => '1',
            'slugBehaviour' => 'unique',
            'storageUidImporter' => '1',
            'tagPid' => '1',
        ],
        'powermail' => [
            'disableBackendModule' => '0',
            'disableIpLog' => '1',
            'disableMarketingInformation' => '0',
            'disablePluginInformation' => '0',
            'disablePluginInformationMailPreview' => '0',
            'enableCaching' => '0',
            'replaceIrreWithElementBrowser' => '0',
        ],
        'scheduler' => [
            'maxLifetime' => '1440',
            'showSampleTasks' => '1',
        ],
        'static_info_tables' => [
            'constraints' => [
                'depends' => [
                    'extbase' => '11.5.0-11.5.99',
                    'extensionmanager' => '11.5.0-11.5.99',
                    'typo3' => '11.5.0-11.5.99',
                ],
            ],
            'enableManager' => '0',
            'entities' => [
                'Country',
                'CountryZone',
                'Currency',
                'Language',
                'Territory',
            ],
            'tables' => [
                'static_countries' => [
                    'isocode_field' => [
                        'cn_iso_##',
                    ],
                    'label_fields' => [
                        'cn_short_##' => [
                            'mapOnProperty' => 'shortName##',
                        ],
                        'cn_short_en' => [
                            'mapOnProperty' => 'shortNameEn',
                        ],
                    ],
                ],
                'static_country_zones' => [
                    'isocode_field' => [
                        'zn_code',
                        'zn_country_iso_##',
                    ],
                    'label_fields' => [
                        'zn_name_##' => [
                            'mapOnProperty' => 'name##',
                        ],
                        'zn_name_local' => [
                            'mapOnProperty' => 'localName',
                        ],
                    ],
                ],
                'static_currencies' => [
                    'isocode_field' => [
                        'cu_iso_##',
                    ],
                    'label_fields' => [
                        'cu_name_##' => [
                            'mapOnProperty' => 'name##',
                        ],
                        'cu_name_en' => [
                            'mapOnProperty' => 'nameEn',
                        ],
                    ],
                ],
                'static_languages' => [
                    'isocode_field' => [
                        'lg_iso_##',
                        'lg_country_iso_##',
                    ],
                    'label_fields' => [
                        'lg_name_##' => [
                            'mapOnProperty' => 'name##',
                        ],
                        'lg_name_en' => [
                            'mapOnProperty' => 'nameEn',
                        ],
                    ],
                ],
                'static_territories' => [
                    'isocode_field' => [
                        'tr_iso_##',
                    ],
                    'label_fields' => [
                        'tr_name_##' => [
                            'mapOnProperty' => 'name##',
                        ],
                        'tr_name_en' => [
                            'mapOnProperty' => 'nameEn',
                        ],
                    ],
                ],
            ],
            'version' => '11.5.5',
        ],
        'vhs' => [
            'disableAssetHandling' => '0',
        ],
    ],
    'FE' => [
        'cacheHash' => [
            'excludedParameters' => [
                'L',
                'pk_campaign',
                'pk_kwd',
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'utm_term',
                'utm_content',
                'gclid',
                'lightbox',
            ],
        ],
        'compressionLevel' => 0,
        'debug' => false,
        'hidePagesIfNotTranslatedByDefault' => '1',
    ],
    'GFX' => [
        'jpg_quality' => '80',
        'processor' => 'GraphicsMagick',
        'processor_allowTemporaryMasksAsPng' => false,
        'processor_colorspace' => 'RGB',
        'processor_effects' => false,
        'processor_enabled' => true,
        'processor_path' => '/usr/bin/',
        'processor_path_lzw' => '/usr/bin/',
    ],
    'MAIL' => [
        'transport' => 'sendmail',
        'transport_sendmail_command' => '/usr/sbin/sendmail -t -i ',
        'transport_smtp_encrypt' => '',
        'transport_smtp_password' => '',
        'transport_smtp_server' => '',
        'transport_smtp_username' => '',
    ],
    'SYS' => [
        'caching' => [
            'cacheConfigurations' => [
                'extbase_object' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend',
                    'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\VariableFrontend',
                    'groups' => [
                        'system',
                    ],
                    'options' => [
                        'defaultLifetime' => 0,
                    ],
                ],
            ],
        ],
        'devIPmask' => '',
        'displayErrors' => 0,
        'encryptionKey' => '945387zhg29ß3h23ßq9heaüwsgvopeuhweß9nfß973hß932fjüqwifohjr8pezhgpwiewefwöäüuef4psöedufnbweiufbwepi74',
        'exceptionalErrors' => 4096,
        'sitename' => 'Ulrich natürlich',
    ],
];
