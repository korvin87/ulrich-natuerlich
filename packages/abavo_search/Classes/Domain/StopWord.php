<?php
/*
 * abavo_search
 * 
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use Abavo\AbavoSearch\Domain\Exception\IndexException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * StopWord
 *
 * @author mbruckmoser
 */
class StopWord implements SingletonInterface
{
    protected $stopWords  = [];
    protected $whiteWords = [];

    public function checkSearchForStopWords(&$search = '', $stopWordXMLFilePaths = [], $whiteWordXMLFilePaths = [])
    {
        $removedWords = [];

        if ($search == '' || empty($stopWordXMLFilePaths) || empty($whiteWordXMLFilePaths)) {
            throw new IndexException('StopWord: No search value and/or xmlFiles configured.');
        } else {

            // Get Lists
            foreach ($stopWordXMLFilePaths as $stopWordXMLFilePath) {
                $this->getWords($this->stopWords, GeneralUtility::getFileAbsFileName($stopWordXMLFilePath.$GLOBALS['TSFE']->getLanguage()->getTwoLetterIsoCode().'.StopWords.xml'));
            }
            foreach ($stopWordXMLFilePaths as $whiteWordXMLFilePaths) {
                $this->getWords($this->whiteWords, GeneralUtility::getFileAbsFileName($stopWordXMLFilePath.$GLOBALS['TSFE']->getLanguage()->getTwoLetterIsoCode().'.WhiteWords.xml'));
            }

            // Compare Lists
            if (!empty($this->whiteWords)) {
                $this->stopWords[key($this->stopWords)] = array_diff(current($this->stopWords), current($this->whiteWords));
            }
        }

        $words = GeneralUtility::trimExplode(' ', $search);
        if (count($words)) {
            foreach ($words as $key => $word) {

                if (is_int(array_search($word, current($this->stopWords)))) {
                    array_push($removedWords, $word);
                    unset($words[$key]);
                }
            }
            $search = implode(' ', $words);
        }

        return implode(', ', $removedWords);
    }

    private function getWords(&$property = [], $wordXMLFilePath = '')
    {
        try {

            if (file_exists($wordXMLFilePath)) {
                $xmlstr = file_get_contents($wordXMLFilePath);
                $xml    = new \SimpleXMLElement($xmlstr);

                foreach ($xml->word as $word) {
                    $property[(string) $word['lang']][] = (string) $word['title'];
                }
            } else {
                throw new IndexException('getWords: No xmlFile '.$wordXMLFilePath);
            }
        } catch (\Exception $e) {
            DebuggerUtility::var_dump($e->getMessage(), 'abavo_search Error');
        }
    }
}