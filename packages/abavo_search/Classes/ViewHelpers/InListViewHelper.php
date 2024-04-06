<?php

namespace Abavo\AbavoSearch\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
/**
 * ViewHelper to check if a value is in a array
 */
class InListViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments.
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('needle', 'string', '', false, '');
        $this->registerArgument('haystack', 'array', '', false, []);
    }

    /**
     * Returns boolean if string in comma separated(by default) list
     *
     * @return boolean if needle exist or not
     */
    public function render()
    {
        if (!is_array($this->arguments['haystack'])) {
            return 0;
        }

        if (in_array($this->arguments['needle'], $this->arguments['haystack'])) {
            return 1;
        } else {
            return 0;
        }
    }
}