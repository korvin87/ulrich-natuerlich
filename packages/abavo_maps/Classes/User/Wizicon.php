<?php
/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

/**
 * Add abavo_maps extension to the wizard in page module
 *
 * @package TYPO3
 * @subpackage tx_abavo_maps
 */
class abavo_maps_pimain_wizicon
{

    /**
     * Processing the wizard items array
     *
     * @param array $wizardItems The wizard items
     * @return array array with wizard items
     */
    public function proc($wizardItems)
    {
        $wizardItems['plugins_tx_abavo_maps'] = ['iconIdentifier' => 'wizard-abavo_maps', 'title' => $GLOBALS['LANG']->sL('LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:pimain_title'), 'description' => $GLOBALS['LANG']->sL('LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:pimain_plus_wiz_description'), 'params' => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=abavomaps_pimain'];

        return $wizardItems;
    }
}
