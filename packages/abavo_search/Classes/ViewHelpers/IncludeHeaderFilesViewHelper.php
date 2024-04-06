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
use TYPO3\CMS\Core\Page\PageRenderer;
/**
 * ViewHelper to include HTML header files (css/js)
 *
 * @author mbruckmoser
 */
class IncludeHeaderFilesViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments.
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('pathToFile', 'string', 'relative to site path to file', false, '');
        $this->registerArgument('fileType', 'string', 'the filetyps (js/css)', false, null);
        $this->registerArgument('rel', 'string', '', false, 'stylesheet');
        $this->registerArgument('type', 'string', 'Content Type', false, 'text/javascript');
        $this->registerArgument('media', 'string', '', false, 'all');
        $this->registerArgument('title', 'string', '', false, '');
        $this->registerArgument('compress', 'boolean', '', false, true);
        $this->registerArgument('forceOnTop', 'boolean', '', false, false);
        $this->registerArgument('allWrap', 'string', '', false, '');
        $this->registerArgument('excludeFromConcatenation', 'boolean', '', false, false);
        $this->registerArgument('footer', 'footer', 'put JS in footer', false, false);
    }

    /**
     * Render method
     * 
     * @return void
     */
    public function render()
    {
        if ($this->arguments['pathToFile'] !== '') {

            if ($this->arguments['fileType'] === null) {
                $this->arguments['fileType'] = strtolower(substr(strrchr($this->arguments['pathToFile'], '.'), 1));
            }

            // Get PageRenderer
            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

            switch ($this->arguments['fileType']) {

                case 'js':
                    if ($this->arguments['footer']) {
                        $pageRenderer->addJsFooterFile(
                            $this->arguments['pathToFile'],
                            $this->arguments['type'],
                            $this->arguments['compress'],
                            $this->arguments['forceOnTop'],
                            $this->arguments['allWrap'],
                            $this->arguments['excludeFromConcatenation']
                        );
                    } else {
                        $pageRenderer->addJsFile(
                            $this->arguments['pathToFile'],
                            $this->arguments['type'],
                            $this->arguments['compress'],
                            $this->arguments['forceOnTop'],
                            $this->arguments['allWrap'],
                            $this->arguments['excludeFromConcatenation']
                        );
                    }
                    break;

                case 'css':
                    $pageRenderer->addCssFile(
                        $this->arguments['pathToFile'],
                        $this->arguments['rel'],
                        $this->arguments['media'],
                        $this->arguments['title'],
                        $this->arguments['compress'],
                        $this->arguments['forceOnTop'],
                        $this->arguments['allWrap'],
                        $this->arguments['excludeFromConcatenation']
                    );
                    break;

                default:
            }
        }
    }
}