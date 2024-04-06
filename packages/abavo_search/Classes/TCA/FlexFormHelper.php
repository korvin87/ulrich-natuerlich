<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\TCA;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * FlexFormHelper
 *
 * @author mbruckmoser
 */
class FlexFormHelper implements SingletonInterface
{

    function getParamsAsFieldList($config)
    {
        if (!empty($config['config']['parameters'])) {

            foreach ($config['config']['parameters'] as $key => $item) {
                $config['items'][] = [
                    0 => $key,
                    1 => $item
                ];
            }
        }

        return $config;
    }

    public function getSysLanguages($config)
    {
        // Parameter items
        if (!empty($config['config']['parameters'])) {
            foreach ($config['config']['parameters'] as $key => $item) {
                $config['items'][] = [
                    0 => $item,
                    1 => $key
                ];
            }
        }

        // Default item
        $config['items'][] = [
            0 => 'Default [0]',
            1 => 0
        ];

        // Syslanguages items
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_language');
        $queryResult  = $queryBuilder->select('*')
            ->from('sys_language')
            ->add('orderBy', 'title ASC')
            ->execute();

        while ($item = $queryResult->fetch()) {
            $config['items'][] = [
                0 => $item['title'].' ['.$item['uid'].']',
                1 => $item['uid']
            ];
        }

        return $config;
    }

    public function renderTranslation($config)
    {
        $args = [];
        if (isset($config['fieldConf']['config']['parameters']['key'])) {

            if (isset($config['fieldConf']['config']['parameters']['args'])) {
                if (isset($config['fieldConf']['config']['parameters']['args']['getenv'])) {
                    $args[] = getenv($config['fieldConf']['config']['parameters']['args']['getenv']) ?: 'undefined';
                } else {
                    $args = $config['fieldConf']['config']['parameters']['args'];
                }
            }

            return LocalizationUtility::translate($config['fieldConf']['config']['parameters']['key'], null, $args);
        }
    }
}