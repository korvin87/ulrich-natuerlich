<?php

namespace Abavo\SitePackage\User;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
class MenuL10nHelper
{
    public const CONFIG_EXT_PI = 'tx_sitepackage';

    protected $gpVarTableMapping = [
        'tx_news_pi1' => [
            'table' => 'tx_news_domain_model_news',
            'idField' => 'news'
        ]
    ];
    protected $gp = [];
    protected $db = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->gp = GeneralUtility::_Get();
        $this->db = $GLOBALS['TYPO3_DB'];

        if ($mapping = $GLOBALS['TSFE']->tmpl->setup['plugin.'][self::CONFIG_EXT_PI . '.']['settings.']['MenuL10nHelper.']['gpVarTableMapping.']) {
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $this->gpVarTableMapping = $typoScriptService->convertTypoScriptArrayToPlainArray($mapping);
        }
    }

    /**
     * hideHiddenExtensionRecords Method
     *
     * @param array $menuArr The Menu items array
     * @param array $conf The menu object
     */
    public function hideHiddenExtensionRecords($menuArr, $conf)
    {

        if (!empty($menuArr)) {

            foreach ($this->gpVarTableMapping as $gpVar => $mapping) {
                if (array_key_exists($gpVar, $this->gp)) {

                    $this->modifyMenuItemsState($mapping['table'], $this->gp[$gpVar][$mapping['idField']], $menuArr);
                }
            }
        }

        #\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($menuArr);
        return $menuArr;
    }

    /**
     * modifyMenuItemsState Method
     *
     * @param string $table The table name
     * @param string $uid The record uid to check
     * @param string $sys_language_uid The sys_language_uid to check
     */
    private function modifyMenuItemsState($table = '', $uid = null, &$menuArr = [])
    {

        foreach ($menuArr as $key => $item) {
            $sys_language_uid = current(GeneralUtility::trimExplode('&L=', $item['_ADD_GETVARS'], true));

            if ($this->translationExist($table, $uid, $sys_language_uid) === false) {
                $menuArr[$key]['ITEM_STATE'] = 'USERDEF1';
            }
        }
    }

    /**
     * translationExist Method
     * Checks if a translation or a original record with sys_language_uid exists
     *
     * @param string $table The table name
     * @param string $uid The record uid to check
     * @param string $sys_language_uid The sys_language_uid to check
     */
    private function translationExist($table = '', $uid = null, $sys_language_uid = null)
    {

        #$this->db->store_lastBuiltQuery = 1;
        $records = $this->db->exec_SELECTgetRows(
            'uid', $table,
            'sys_language_uid=' . (int)$sys_language_uid . ' AND (uid=' . (int)$uid . ' OR l10n_parent=' . (int)$uid . ') AND deleted=0 AND hidden=0 AND (starttime = 0 OR starttime >= NOW()) AND (endtime = 0 OR endtime < NOW())',
            '', '', 1);
        #\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->db->debug_lastBuiltQuery, $sys_language_uid);

        return (boolean)(is_countable($records) ? count($records) : 0);
    }
}