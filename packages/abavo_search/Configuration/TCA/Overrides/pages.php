<?php
defined('TYPO3') or die();

// Custom Page Icon
$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
    0 => 'abavo Suche - Datensatzsammlung',
    1 => 'abavo_search',
    2 => 'apps-pagetree-folder-contains-abavo_search'
];

$GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-abavo_search'] = 'apps-pagetree-folder-contains-abavo_search';