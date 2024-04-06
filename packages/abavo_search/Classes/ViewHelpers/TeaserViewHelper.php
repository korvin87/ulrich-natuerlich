<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
/**
 * TeaserViewHelper
 *
 * @author mbruckmoser
 */
class TeaserViewHelper extends AbstractViewHelper
{
    /**
     * As this ViewHelper renders HTML, the output must not be escaped.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initialize arguments.
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('content', 'string', 'the content to crop', false, '');
        $this->registerArgument('maxResultChars', 'int', '', false, 600);
        $this->registerArgument('arrSWords', 'array', '', false, []);
        $this->registerArgument('synonyms', 'array', '', false, []);
        $this->registerArgument('highlightSword', 'boolean', '', false, true);
    }

    /**
     * Renders a Teaser
     *
     * Forked from ke_search\Classes\lib\class.tx_kesearch_lib_searchresult.php
     * @return string the rendered string
     */
    public function render()
    {
        $amountOfSearchWords        = is_countable($this->arguments['arrSWords']) ? count($this->arguments['arrSWords']) : 0;
        $this->arguments['content'] = strip_tags($this->arguments['content']);
        $teaserArray                = [];


        // with each new searchword and all the croppings here the teaser for each word will become too small/short
        // I decided to add 20 additional letters for each searchword. It looks much better and is more readable
        $charsForEachSearchWord     = ceil($this->arguments['maxResultChars'] / $amountOfSearchWords) + 20;
        $charsBeforeAfterSearchWord = ceil($charsForEachSearchWord / 2);
        $aSearchWordWasFound        = FALSE;
        $isSearchWordAtTheBeginning = FALSE;

        $searchParts = [];
        foreach ($this->arguments['arrSWords'] as $search) {

            $searchParts = explode(' ', $search);
            foreach ($searchParts as $word) {

                //Check if word is in other content of $teaserArray
                if ($this->isWordInTeaserContent($word, $teaserArray) === true) {
                    continue;
                }

                $word = ' '.$word; // our searchengine searches for wordbeginnings
                $pos  = stripos($this->arguments['content'], $word);
                if ($pos === FALSE) {
                    // if the word was not found it could be within brakets => (searchWord)
                    // so give it a second try
                    $pos = stripos($this->arguments['content'], trim($word));
                    if ($pos === FALSE) {
                        continue;
                    }
                }
                $aSearchWordWasFound = TRUE;

                // if searchword is the first word
                if ($pos === 0) {
                    $isSearchWordAtTheBeginning = TRUE;
                }

                // find search starting point
                $startPos = $pos - $charsBeforeAfterSearchWord;
                if ($startPos < 0) $startPos = 0;

                // crop some words behind searchword
                $partWithSearchWord = substr($this->arguments['content'], $startPos);
                $temp               = $GLOBALS['TSFE']->cObj->crop($partWithSearchWord, $charsForEachSearchWord.'|...|1');



                // crop some words before searchword
                // after last cropping our text is too short now. So we have to find a new cutting position
                ($startPos > 10) ? $length        = strlen($temp) - 10 : $length        = strlen($temp);
                $teaserArray[] = $GLOBALS['TSFE']->cObj->crop($temp, '-'.$length.'||1');
            }
        }

        // When the searchword was found in title but not in content the teaser is empty
        // in that case we have to get the first x letters without containing any searchword
        if ($aSearchWordWasFound === FALSE) {
            $teaser = $GLOBALS['TSFE']->cObj->crop($this->arguments['content'], $this->arguments['maxResultChars'].'||1');
        } elseif ($isSearchWordAtTheBeginning === TRUE) {
            $teaser = implode(' ', $teaserArray);
        } else {
            $teaser = '...'.implode(' ', $teaserArray);
        }

        // Cleanup invalid characters
        $teaser = iconv("UTF-8", "UTF-8//IGNORE", $teaser);

        // highlight hits?
        if ($this->arguments['highlightSword']) {
            $teaser = $this->highlightArrayOfWordsInContent($searchParts, $this->arguments['synonyms'], $teaser);
        }

        return $teaser;
    }

    /**
     * Find and highlight the searchwords
     *
     * @param array $wordArray
     * @param array $synonyms
     * @param string $content
     * @return string The content with highlighted searchwords
     */
    public function highlightArrayOfWordsInContent($wordArray, $synonyms, $content)
    {
        if (is_array($wordArray) && count($wordArray)) {

            foreach ($wordArray as $word) {


                $firstPart = '###START###';
                if (!empty($synonyms)) {
                    $firstPart = (in_array($word, $synonyms)) ? '###STARTSYM###' : $firstPart;
                }

                $word = str_replace('/', '\/', $word);
                $word = htmlspecialchars($word);

                $matches = [];
				preg_match('/\b(' . $word . ')/iu', $content, $matches);
				if ($matches) {
					foreach ($matches as $match) {
						$content = str_replace($match, $firstPart.$match.'###END###', $content);
					}
				}
            }

            $content = str_replace(
                ['###START###', '###STARTSYM###', '###END###'], ['<span class="search-hit">', '<span class="search-hit synonym">', '</span>'], $content
            );
        }
        return $content;
    }

    /**
     * Check if word is in other content of $teaserArray
     *
     * @param string $word
     * @param array $teaserArray
     * @return boolean
     */
    private function isWordInTeaserContent($word, &$teaserArray)
    {
        $isInTeaserContent = false;
        if (is_array($teaserArray) && count($teaserArray)) {
            foreach ($teaserArray as $teaserContent) {
                if (stripos($teaserContent, trim($word))) {
                    $isInTeaserContent = true;
                }
            }
        }

        return $isInTeaserContent;
    }
}
