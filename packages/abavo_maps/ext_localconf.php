<?php
if (!defined('TYPO3')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'AbavoMaps',
	'Pimain',
	[\TYPO3\AbavoMaps\Controller\MapController::class => 'show'],
	// non-cacheable actions
	[\TYPO3\AbavoMaps\Controller\MapController::class => 'show', \TYPO3\AbavoMaps\Controller\MarkerController::class => 'update']
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'AbavoMaps', 'PiEid',
    // cacheable actions
    [\TYPO3\AbavoMaps\Controller\MarkerController::class => 'update'],
    // non-cacheable actions
    [\TYPO3\AbavoMaps\Controller\MarkerController::class => 'update']
);

// Register eID
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['abavo_maps'.'_ajaxDispatcher'] = 'EXT:'.'abavo_maps'.'/Classes/User/EidBootstrap.php';


// SCHEDULER TASK ENTRY
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['AbavoMapsGeocode'] = ['extension'        => 'abavo_maps', 'title'            => 'abavo Maps Geocode', 'description'      => 'Geocode specific data records with limit per IP per day: https://developers.google.com/maps/articles/geocodestrat#quota-limits', 'additionalFields' => 'AbavoMapsGeocodeAddfields'];

/*
 * Register icons
 */
if (TYPO3_MODE === 'BE') {
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

    $icons = [
         \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class => [
             'wizard-abavo_maps' => 'abavo_maps.svg',
             'apps-pagetree-folder-contains-abavo_maps' => 'tx_abavomaps_domain_model_map.svg'
        ]
    ];

    foreach ($icons as $provider => $group) {
        foreach ($group as $identifier => $file) {
            $iconRegistry->registerIcon(
                $identifier,
                $provider,
                ['source' => 'EXT:abavo_maps/Resources/Public/Icons/'.$file]
            );
        }
    }
}