<?php
defined('TYPO3') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
   'ulrich_products',
   'tx_ulrichproducts_domain_model_product',
   'categories',
   [
        'l10n_mode' => 'exclude',
        'fieldConfiguration' => [
            'autoSizeMax' => 10,
            'minitems' => 1
        ]
   ]
);
