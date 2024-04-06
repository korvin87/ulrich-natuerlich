<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
/**
 * ExtendedIfViewHelper
 *
 * @author mbruckmoser
 */
class ExtendedIfViewHelper extends AbstractConditionViewHelper
{

    /**
     * Initialize arguments.
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('conditions', 'array', 'View helper conditions', false, []);
        $this->registerArgument('condition', 'string', 'View helper condition', false, 'and');
    }

    /**
     * renders <f:then> child if $condition and $and is true, otherwise renders <f:else> child.
     *
     * @return string the rendered string
     */
    public function render()
    {

        switch ((string) $this->arguments['condition']) {

            case 'and':
                foreach ($this->arguments['conditions'] as $con) {
                    if (!$con) {
                        return $this->renderElseChild();
                    }
                }
                return $this->renderThenChild();
                break;

            case 'or':
                if ((is_countable($this->arguments['conditions']) ? count($this->arguments['conditions']) : 0) == 2 && ($this->arguments['conditions'][0] || $this->arguments['conditions'][1])) {
                    return $this->renderThenChild();
                } else {
                    return $this->renderElseChild();
                }
                break;
            default:
            //do nothing
        }
    }
}