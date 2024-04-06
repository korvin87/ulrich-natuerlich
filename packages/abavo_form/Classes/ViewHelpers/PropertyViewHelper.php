<?php
/*
 * abavo_form
 * 
 * @copyright   2016 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
/**
 * Description of PropertyViewHelper
 *
 * @author mbruckmoser
 */
class PropertyViewHelper extends AbstractViewHelper
{

    /**
     * @return mixed
     */
    public function render()
    {
        $propertyName = $this->arguments['propertyName'];
        $subject = $this->arguments['subject'];
        if ($subject === NULL) {
            $subject = $this->renderChildren();
        }
        return ObjectAccess::getPropertyPath($subject, $propertyName);
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('propertyName', 'string', '', true);
        $this->registerArgument('subject', 'mixed', '', false, 'NULL');
    }
}