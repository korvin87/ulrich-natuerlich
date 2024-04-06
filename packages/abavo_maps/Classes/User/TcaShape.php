<?php

namespace TYPO3\AbavoMaps\User;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Core\Information\Typo3Version;
/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
class TcaShape
{

    public function renderMap($PA, $fObj)
    {
        /*
         * Get TypoScript-Settings
         */
        $objectManager        = GeneralUtility::makeInstance(ObjectManager::class);
        $configurationManager = $objectManager->get(ConfigurationManager::class);
        $ts                   = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $typoScriptService    = $objectManager->get(TypoScriptService::class);
        $config               = $typoScriptService->convertTypoScriptArrayToPlainArray($ts);
        $settings             = $config['plugin']['tx_abavomaps']['settings'];

        // get API-KEY
        $apiKeyParam = ($settings['gmApiKey'] != '') ? 'key='.$settings['gmApiKey'].'&' : '';


        $SHAPE_BODY = (VersionNumberUtility::convertVersionNumberToInteger(GeneralUtility::makeInstance(Typo3Version::class)->getBranch()) >= VersionNumberUtility::convertVersionNumberToInteger('7.0')) ? current($PA['row']['body']) : $PA['row']['body'];

        $mapContent = "
			<div id='map-canvas' style='width:450px;height:300px;display:none;'></div>
			<script type='text/javascript' src='https://maps.googleapis.com/maps/api/js?".$apiKeyParam."v=3.exp&libraries=geometry,places,drawing'></script>
			<script type='text/javascript' src='/typo3conf/ext/abavo_maps/Resources/Public/Js/Vendor/Bramus/google-maps-polygon-moveto/js/google.maps.Polygon.getBounds.js'></script>
			<script type='text/javascript' src='/typo3conf/ext/abavo_maps/Resources/Public/Js/Vendor/Bramus/google-maps-polygon-moveto/js/google.maps.Polygon.moveTo.js'></script>
			<script type='text/javascript' src='/typo3conf/ext/abavo_maps/Resources/Public/Js/TcaGm.js'></script>

			<script>
				CUR_UID = '".$PA['row']['uid']."';
				FIELD_NAME = 'tx_abavomaps_domain_model_shape';
				FIELD_ID = 'abavo_maps_autocomplete';
				FIELD_CONFIG = 'abavo_maps_config';
				ADD_MARKER = false;
				ERROR_MSG_GEOCODE = 'Die Adresse konnte aus folgendem Grund nicht ermittelt werden';
				AUTOCOMPLETE_ENABLED = true;
				AUTOCOMPLETE_FIELD = 'abavo_maps_autocomplete';
				BOUNDS = '".$PA['row']['config']."';
				SHAPE_BODY = '".$SHAPE_BODY."';
				COLOR = '".$PA['row']['color']."';
				
				initMap();				
			</script>
		";

        $inputFieldContent = '
		<span class="t3-form-field-container">			
			<div class="t3-form-field-item">
				<div style="overflow:hidden; width:412px; height:20px;" class="t3-tceforms-fieldReadOnly" title="">
					<input id="abavo_maps_config" type="text" style="position:relative;top:-1px;border:1px solid #fff;width:410px;height:16px;margin:0px;padding:0px" readonly="readonly" name="'.$PA['itemFormElName'].'" value="'.htmlspecialchars($PA['itemFormElValue']).'"/>
					<span class="t3-icon t3-icon-status t3-icon-status-status t3-icon-status-readonly">&nbsp;</span>
				</div>
			</div>
		</span>';

        $formField = '<div>';
        $formField .= '<input id="abavo_maps_autocomplete" type="text" style="width:374px;" placeholder="Bitte einen Ort eingeben"';
        $formField .= ' onchange="codeAddress();'.htmlspecialchars(implode('', $PA['fieldChangeFunc'])).'"';
        $formField .= $PA['onFocus'];
        $formField .= ' /><input type="button" value="Finden" onclick="codeAddress()"><br>'.$mapContent.$inputFieldContent.'</div>';
        return $formField;
    }
}