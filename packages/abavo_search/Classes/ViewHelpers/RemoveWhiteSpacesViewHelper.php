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
 * ViewHelper to remove whitespaces
 *
 * @author mbruckmoser
 */
class RemoveWhiteSpacesViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments.
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('content', 'string', 'The search string', false, '');
    }

    /**
     * Render Method
     * 
     * @return string
     */
    public function render()
    {
        return preg_replace("/\s\s+/", "", $this->arguments['content']);
    }
}