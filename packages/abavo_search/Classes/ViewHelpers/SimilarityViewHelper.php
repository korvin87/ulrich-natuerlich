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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Abavo\AbavoSearch\User\SearchUtility;
/**
 * ViewHelper to render Similarity of search terms
 *
 * @author mbruckmoser
 */
class SimilarityViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments.
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('arrContent', 'array', 'The full content for similarity comparison', false, []);
        $this->registerArgument('search', 'string', 'The search string', false, '');
    }

    /**
     * Render Method
     * 
     * @return int $similarity The highest similarity in percent
     */
    public function render()
    {
        $search = null;
        if (!empty($this->arguments['arrContent']) && $search != '') {

            $allContent    = implode(' ', $this->arguments['arrContent']); // Merge all content to one string
            $searchUtility = GeneralUtility::makeInstance(SearchUtility::class);
            return $searchUtility->calculateSimilarty($allContent, $this->arguments['search']);
        }
    }
}