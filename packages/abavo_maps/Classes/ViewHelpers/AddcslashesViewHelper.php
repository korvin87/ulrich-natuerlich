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
class AddcslashesViewHelper extends AbstractViewHelper
{

    /**
     * use php function addcslashes
     *
     * @return  string	a converted string
     */
    public function render()
    {
        $data = $this->arguments['data'];
        return addcslashes(preg_replace('/\s\s+/', ' ', $data), "'");
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('data', 'mixed', '', true);
    }
}
