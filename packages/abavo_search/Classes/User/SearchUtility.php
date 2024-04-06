<?php
/*
 * abavo_search
 * 
 * @copyright   2017 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\User;

use Abavo\AbavoSearch\Domain\Service\IndexSearchService;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SearchUtility
 *
 * @author mbruckmoser
 */
class SearchUtility
{

    /**
     * Calculate a similarty to a string against search word´s
     *
     * @param string $content
     * @param string $keyWords whitespace seperated
     * @param int $round round to this decimal places
     * @return float
     */
    public function calculateSimilarty($content = '', $keyWords = '', $round = 2)
    {
        $percents = [];
        foreach (GeneralUtility::trimExplode(' ', $keyWords, true) as $keyword) {
            foreach (GeneralUtility::trimExplode(' ', $content, true) as $word) {

                if (strlen($word) >= 3) {
                    $percent = 0;
                    similar_text($keyword, $word, $percent);
                    if ($percent !== (float) 0 && !in_array($percent, $percents)) {
                        $percents[] = $percent;
                    }
                }
            }
        }


        // calc average
        $arraySum       = array_sum($percents);
        $arrayCount     = count($percents);
        $averagePercent = ($arraySum !== $arrayCount) ? $arraySum / count($percents) : 100;

        return round($averagePercent, (int) $round);
    }

    /**
     * Get all registered search services as facet array
     * This is used by search form
     *
     * @return array
     */
    public function getRegisteredSearchServicesAsFacet()
    {
        $searchServices = [];
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['searchServiceClasses'] as $serviceName => $conf) {

            $label = 'label undefined in search service config';
            if (is_array($conf) && isset($conf['label']) && isset($conf['label'][$GLOBALS['TSFE']->lang])) {
                $label = $conf['label'][$GLOBALS['TSFE']->lang];
            }

            if ($serviceName === IndexSearchService::class) {
                $label = LocalizationUtility::translate('label.searchService', 'AbavoSearch');
            }
            $searchServices[md5($serviceName)] = $label;
        }
        return $searchServices;
    }
}