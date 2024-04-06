<?php
/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * class.ext_update.php
 * 
 * @author  Mathias Bruckmoser
 * @package TYPO3
 * @subpackage abavo_maps
 */
class ext_update
{
    /**
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $databaseConnection;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->databaseConnection = $GLOBALS['TYPO3_DB'];
    }

    /**
     * Called by the extension manager to determine if the update menu entry
     * should by showed.
     *
     * @return bool
     * @todo find a better way to determine if update is needed or not.
     */
    public function access()
    {
        return TRUE;
    }

    /**
     * Main function, returning the HTML content of the module
     *
     * @return	string		HTML
     */
    function main()
    {

        $content = '';

        // Info Message
        $flashMessage = GeneralUtility::makeInstance(FlashMessage::class,
                'This update script allows you to import DB entrys from old abavo_gm in new abavo_maps extension. This script only imports the raw records including FAL and also marker to map relations, <u>but without PLUGIN to map flexform relations.</u>',
                'Usage hint', AbstractMessage::INFO);
        $content .= $flashMessage->render();

        // Show form if 'abavo_gm' is loaded
        if (in_array('abavo_gm', ExtensionManagementUtility::getLoadedExtensionListArray())) {

            /*
             * Entry Part
             */
            if (!GeneralUtility::_GP('do_update')) {

                $flashMessage = GeneralUtility::makeInstance(FlashMessage::class, 'Make a DB-Backup <b>BEFORE</b> use "import".', 'Important note',
                        AbstractMessage::WARNING);
                $content .= $flashMessage->render();

                // Counting DB Records
                $res         = $this->databaseConnection->exec_SELECTquery('uid, pid, title', 'tx_abavogm_domain_model_googlemap', 'title != \'\' AND deleted = 0');
                $messageBody = 'Map-Records: '.$this->databaseConnection->sql_num_rows($res);

                $res = $this->databaseConnection->exec_SELECTquery('uid, pid, title', 'tx_abavogm_domain_model_marker', 'title != \'\' AND deleted = 0');
                $messageBody .= '<br>Marker-Records: '.$this->databaseConnection->sql_num_rows($res);

                $res = $this->databaseConnection->exec_SELECTquery('uid', 'sys_file_reference', '`tablenames`="tx_abavogm_domain_model_googlemap"');
                $messageBody .= '<br>Map sys_file_reference: '.$this->databaseConnection->sql_num_rows($res);
                $res = $this->databaseConnection->exec_SELECTquery('uid', 'sys_file_reference', '`tablenames`="tx_abavogm_domain_model_marker"');
                $messageBody .= '<br>Marker sys_file_reference: '.$this->databaseConnection->sql_num_rows($res);

                // Entry Notice about records
                $flashMessage = GeneralUtility::makeInstance(FlashMessage::class, $messageBody, 'Counted DB records', AbstractMessage::NOTICE);
                $content .= $flashMessage->render();

                $content .= '<form action="'.GeneralUtility::getIndpEnv('REQUEST_URI').'" method="POST"><input type="hidden" name="do_update" value="1"/>';
                $content .= '<input type="submit" name="do_update" value="IMPORT!" style="color: #fff; background-color: #f00;" />';

                /*
                 * Update Part
                 */
            } else {

                try {
                    $content .= $this->importMapRecords();
                    $content .= $this->importMarkerRecords();
                } catch (\Exception $e) {
                    // Entry Notice about records
                    $flashMessage = GeneralUtility::makeInstance(FlashMessage::class,
                            'Import from tx_abavogm_domain_model_googlemap failed:'.$e->getMessage(), 'Import Error', AbstractMessage::ERROR);
                    $content .= $flashMessage->render();
                }
            }
        } else {
            $flashMessage = GeneralUtility::makeInstance(FlashMessage::class, 'The extension abavo_gm is not loaded. So no import available.',
                    'Nothing to import', AbstractMessage::NOTICE);
            $content .= $flashMessage->render();
        }

        return $content;
    }

    /**
     * Import Map Records
     *
     * @return	boolean
     */
    private function importMapRecords()
    {

        foreach ($this->databaseConnection->exec_SELECTquery('*', 'tx_abavogm_domain_model_googlemap') as $gmMap) {

            //Modify data
            $recordUidOld = $gmMap['uid'];
            unset($gmMap['uid']);
            unset($gmMap['sizeformatwidth']);
            unset($gmMap['sizeformatheight']);
            $this->databaseConnection->exec_INSERTquery('tx_abavomaps_domain_model_map', $gmMap);
            $recordUidNew = $this->databaseConnection->sql_insert_id();

            // GET AND INSERT sys_file_reference
            foreach ($this->databaseConnection->exec_SELECTquery('*', 'sys_file_reference', 'uid_foreign='.$recordUidOld.' AND `tablenames`="tx_abavogm_domain_model_googlemap"') as $sysFileRef) {

                //Modify data
                unset($sysFileRef['uid']);
                $sysFileRef['uid_foreign'] = $recordUidNew;
                $sysFileRef['tablenames']  = 'tx_abavomaps_domain_model_map';
                $this->databaseConnection->exec_INSERTquery('sys_file_reference', $sysFileRef);
            }

            // GET AND INSERT map to marker relations
            $this->databaseConnection->sql_query('ALTER TABLE `tx_abavomaps_map_marker_mm` ADD COLUMN `import_uid_foreign` INT(11) UNSIGNED NOT NULL DEFAULT "0"');

            foreach ($this->databaseConnection->exec_SELECTquery('*', 'tx_abavogm_googlemap_marker_mm', 'uid_local='.$recordUidOld) as $mapMarkerMM) {

                //Modify data
                $mapMarkerMM['uid_local']          = $recordUidNew;
                $mapMarkerMM['import_uid_foreign'] = $mapMarkerMM['uid_foreign'];
                $mapMarkerMM['uid_foreign']        = 0;
                $this->databaseConnection->exec_INSERTquery('tx_abavomaps_map_marker_mm', $mapMarkerMM);
            }
        }

        $flashMessage = GeneralUtility::makeInstance(FlashMessage::class,
                'Import from tx_abavogm_domain_model_googlemap completed. <br> Fields <i>sizeformatwidth</i> and <i>sizeformatheight</i> are obsolet.',
                'Import Map Records Successful', AbstractMessage::OK);
        return $flashMessage->render();
    }

    /**
     * Import Map Records
     *
     * @return	boolean
     */
    private function importMarkerRecords()
    {

        foreach ($this->databaseConnection->exec_SELECTquery('*', 'tx_abavogm_domain_model_marker') as $gmMarker) {
            //Modify data
            $recordUidOld = $gmMarker['uid'];
            unset($gmMarker['uid']);
            $this->databaseConnection->exec_INSERTquery('tx_abavomaps_domain_model_marker', $gmMarker);
            $recordUidNew = $this->databaseConnection->sql_insert_id();

            // GET AND INSERT sys_file_reference
            foreach ($this->databaseConnection->exec_SELECTquery('*', 'sys_file_reference', 'uid_foreign='.$recordUidOld.' AND `tablenames`="tx_abavogm_domain_model_marker"') as $sysFileRef) {

                //Modify data
                unset($sysFileRef['uid']);
                $sysFileRef['uid_foreign'] = $recordUidNew;
                $sysFileRef['tablenames']  = 'tx_abavomaps_domain_model_marker';
                $this->databaseConnection->exec_INSERTquery('sys_file_reference', $sysFileRef);
            }

            // Update negative marker relations
            $mapMarkerMM['uid_local'] = $recordUidNew;
            $this->databaseConnection->exec_UPDATEquery('tx_abavomaps_map_marker_mm', 'import_uid_foreign='.$recordUidOld, ['uid_foreign' => $recordUidNew]);
        }

        // Clean MM-Table
        $this->databaseConnection->sql_query('ALTER TABLE `tx_abavomaps_map_marker_mm` DROP COLUMN `import_uid_foreign`');

        $flashMessage = GeneralUtility::makeInstance(FlashMessage::class, 'Import from tx_abavogm_domain_model_marker completed.',
                'Import Marker Records Successful', AbstractMessage::OK);
        return $flashMessage->render();
    }
}
if (defined('TYPO3') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/abavo_maps/class.ext_update.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/abavo_maps/class.ext_update.php']);
}
