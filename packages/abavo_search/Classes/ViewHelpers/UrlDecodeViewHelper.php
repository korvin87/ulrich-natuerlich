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
 * UrlDecodeViewHelper
 *
 * @author mbruckmoser
 */
class UrlDecodeViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments.
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('content', 'string', '', false, null);
    }

    /**
     * The render method
     * 
     * @return string
     */
    public function render()
    {
        $content = $this->arguments['content'];
        if (NULL === $content) {
            $content = $this->renderChildren();
        }
        return rawurldecode($content);
    }
}