<?php
defined('TYPO3') or die();

// Custom Page Icon
$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
    0 => 'abavo Maps - Datensatzsammlung',
    1 => 'abavo_maps',
    2 => 'apps-pagetree-folder-contains-abavo_maps'
];

$GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-abavo_maps'] = 'apps-pagetree-folder-contains-abavo_maps';
