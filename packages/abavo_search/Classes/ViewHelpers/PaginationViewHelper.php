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
 * ViewHelper to render Paginations
 *
 * @author mbruckmoser
 */
class PaginationViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments.
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('itemsPerPage', 'int', '', false, 10)
            ->registerArgument('resultsCount', 'int', '', false, 0)
            ->registerArgument('currentPage', 'int', '', false, 1)
            ->registerArgument('maxPages', 'int', '', false, 9);
    }

    /**
     * The render method
     * 
     * @return mixed
     */
    public function render()
    {
        $itemsPerPage = $this->arguments['itemsPerPage'];
        $resultsCount = $this->arguments['resultsCount'];
        $currentPage  = $this->arguments['currentPage'];
        $maxPages     = $this->arguments['maxPages'];


        $currentMaxPages = $maxPages;
        $startPages      = 1;
        $offset          = floor($maxPages / 2);
        $maxPages        = (int) $maxPages;

        $pagination = [];

        if ((int) $itemsPerPage > 0 && is_int($resultsCount)) {

            //build paginator
            $pagesCount               = (int) ceil($resultsCount / $itemsPerPage);
            $pagination['pagesCount'] = $pagesCount;

            //Build next / prev links
            if ($currentPage > 1) {
                $pagination['prev'] = $currentPage - 1;
            }
            if ($currentPage < $pagesCount) {
                $pagination['next'] = $currentPage + 1;
            }

            //build the firstpage / lastPage links
            if ($currentPage > 1) {
                $pagination['first'] = 1;
            }
            if ($currentPage < $pagesCount) {
                $pagination['last'] = $pagesCount;
            }

            //write the current page
            $pagination['current'] = $currentPage;

            // Limit pagination
            if (($currentPage + $offset) > $maxPages) {
                $currentMaxPages = $currentPage + $offset;
                $startPages      = $currentPage - $maxPages + $offset + 1;

                // change start, if current page is in range of end-offset
                if (($currentPage + $offset - $pagesCount) > 0) {
                    $startPages -= $currentPage + $offset - $pagesCount;
                }
            }

            // if more next pages available
            if (($pagesCount > $maxPages) && ($currentPage) < ($pagesCount - $offset)) {
                $pagination['moreNext'] = $pagesCount - $currentMaxPages;
            }

            // if more prev pages available
            if ($startPages > 1) {
                $pagination['morePrev'] = true;
            }

            //Now we bild the page navigation
            for ($i = $startPages; $i <= $pagesCount; $i++) {
                if ($i <= $currentMaxPages) {
                    $pagination['pages'][$i]['text'] = $i;
                }
            }
        }

        // Add pagination to variables
        $this->templateVariableContainer->add('pagination', $pagination);
    }
}