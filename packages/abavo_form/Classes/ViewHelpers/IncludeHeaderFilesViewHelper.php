<?php
/*
 * abavo_form
 *
 * @copyright   2016 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
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
     * Render function
     *
     * @return void
     */
    public function render()
    {
        $pathToFile = $this->arguments['pathToFile'];
        $fileType = $this->arguments['fileType'];
        $rel = $this->arguments['rel'];
        $type = $this->arguments['type'];
        $media = $this->arguments['media'];
        $title = $this->arguments['title'];
        $compress = $this->arguments['compress'];
        $forceOnTop = $this->arguments['forceOnTop'];
        $allWrap = $this->arguments['allWrap'];
        $excludeFromConcatenation = $this->arguments['excludeFromConcatenation'];
        $footer = $this->arguments['footer'];
        if ($pathToFile != '') {

            if (NULL === $fileType) {
                $fileType = strtolower(substr(strrchr($pathToFile, '.'), 1));
            }

            // Get PageRenderer
            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

            switch ($fileType) {

                case 'js':
                    if ($footer) {
                        $pageRenderer->addJsFooterFile($pathToFile, $type, $compress, $forceOnTop, $allWrap, $excludeFromConcatenation);
                    } else {
                        $pageRenderer->addJsFile($pathToFile, $type, $compress, $forceOnTop, $allWrap, $excludeFromConcatenation);
                    }
                    break;

                case 'css':
                    $pageRenderer->addCssFile($pathToFile, $rel, $media, $title, $compress, $forceOnTop, $allWrap, $excludeFromConcatenation);
                    break;

                default:
            }
        }
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('pathToFile', 'string', 'relative to site path to file', false, '');
        $this->registerArgument('fileType', 'string', 'the filetyps (js/css)', false);
        $this->registerArgument('rel', 'string', '', false, 'stylesheet');
        $this->registerArgument('type', 'string', 'Content Type', false, 'text/javascript');
        $this->registerArgument('media', 'string', '', false, 'all');
        $this->registerArgument('title', 'string', '', false, '');
        $this->registerArgument('compress', 'boolean', '', false, true);
        $this->registerArgument('forceOnTop', 'boolean', '', false, false);
        $this->registerArgument('allWrap', 'string', '', false, '');
        $this->registerArgument('excludeFromConcatenation', 'boolean', '', false, false);
        $this->registerArgument('footer', 'boolean', 'put JS in footer', false, false);
    }
}