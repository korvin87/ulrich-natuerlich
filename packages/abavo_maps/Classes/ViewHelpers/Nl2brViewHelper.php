<?php

namespace TYPO3\AbavoMaps\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
/**
 * ViewHelper to add cslashes to a string
 */
class Nl2brViewHelper extends AbstractViewHelper
{

    /**
     * use php function addcslashes
     *
     * @param   string	the string to convert
     * @return  string	a converted string
     */
    public function render()
    {
        return preg_replace("/\r|\n/", "", nl2br($this->renderChildren()));
    }
}