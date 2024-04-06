<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Hooks;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use Abavo\AbavoSearch\Domain\Exception\IndexException;

/**
 * IndexModifier Example Class
 *
 * @author mbruckmoser
 */
class ExampleIndexModifier
{

    function modifyIndex(&$recordObj, &$title, &$abstract, &$content)
    {
        try {
            $title = 'MyPrefix: '.$title;
            
        } catch (\Exception $e) {
            DebuggerUtility::var_dump($e->getMessage(), 'ExampleIndexModifier');
        }
    }
}