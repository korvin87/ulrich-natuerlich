<?php

namespace Abavo\AbavoSearch\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
/**
 * Returns the value of an indexed object attribute, effectively avoiding variable nesting.
 *
 * Define this in views as follows:
 * {namespace sws = \SimpleWebSolutions\SwsTools\ViewHelpers}
 * <vh:object.indexed object="{object}" indexedAttribute="attribute{i}" />
 */
class IndexedViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments.
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('object', 'mixed', 'The object to operate on', false, null);
        $this->registerArgument('indexedAttribute', 'string', 'he attribute that is to be fetched', false, null);
    }

    /**
     * Returns the value of an indexed object attribute (workaround for invalid {object.{index}} construct)
     */
    public function render()
    {
        if (is_object($this->arguments['object'])) {
            $method = 'get'.ucfirst($this->arguments['indexedAttribute']);
            return $this->arguments['object']->$method();
        }

        if (is_array($this->arguments['object'])) {
            return $this->arguments['object'][$this->arguments['indexedAttribute']];
        }
    }
}