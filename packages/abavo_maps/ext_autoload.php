<?php
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
$extensionPath = ExtensionManagementUtility::extPath('abavo_maps');
return ['AbavoMapsGeocode' => $extensionPath.'Classes/Tasks/Geocode.php', 'AbavoMapsGeocodeAddfields' => $extensionPath.'Classes/Tasks/GeocodeAddFields.php'];
?>
