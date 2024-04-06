<?php

namespace TYPO3\AbavoMaps\User;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Information\Typo3Version;
/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
class TcaMarker
{

    public function renderMap($PA, $fObj)
    {

        if (ExtensionManagementUtility::isLoaded('abavo_maps') === true) {

            /*
             * Defaults
             */
            $conf       = $PA['parameters'];
            $formField  = '';
            $mapContent = '';

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

            // Additional functionality in TcaGm.js
            if (GeneralUtility::makeInstance(Typo3Version::class)->getBranch() === '7.5') {
                $mapContent.="<script type='text/javascript' src='https://code.jquery.com/jquery-1.11.3.min.js'></script>";
            }


            $mapContent.="
				<div id='map-canvas' style='width:450px;height:300px;display:none;'></div>
				<script type='text/javascript' src='https://maps.google.com/maps/api/js?".$apiKeyParam."libraries=places'></script>
				<script type='text/javascript' src='/typo3conf/ext/abavo_maps/Resources/Public/Js/TcaGm.js'></script>
				<script>
					curLatitude = '".$PA['row']['latitude']."';
					curLongitude = '".$PA['row']['longitude']."';
					CUR_UID = '".$PA['row']['uid']."';
					
					FIELD_NAME = '".$PA['parameters']['FIELD_NAME']."';
					FIELD_ID = '".$PA['parameters']['FIELD_ID']."';
					ADD_MARKER = '".$PA['parameters']['ADD_MARKER']."';
					ERROR_MSG_GEOCODE = '".$PA['parameters']['ERROR_MSG_GEOCODE']."';
					AUTOCOMPLETE_ENABLED = '".$PA['parameters']['AUTOCOMPLETE_ENABLED']."';
					AUTOCOMPLETE_FIELD = '".$PA['parameters']['AUTOCOMPLETE_FIELD']."';
                    TYPO3_BRANCH = '".GeneralUtility::makeInstance(Typo3Version::class)->getBranch()."';

					if ((curLatitude > 0) && (curLongitude > 0)){
						initMap();
					}
				</script>
			";

            $formField = '<div>';
            $formField .= '<input id="'.$PA['parameters']['FIELD_ID'].'" type="text" style="width:374px;" placeholder="Bitte einen Ort eingeben" name="'.$PA['itemFormElName'].'"';
            $formField .= ' value="'.htmlspecialchars($PA['itemFormElValue']).'"';
            $formField .= ' onchange="codeAddress();'.htmlspecialchars(implode('', $PA['fieldChangeFunc'])).'"';
            $formField .= $PA['onFocus'];
            $formField .= ' /><input type="button" value="Finden" onclick="codeAddress()"><br>'.$mapContent.'</div>';
            return $formField;
        }
    }
}