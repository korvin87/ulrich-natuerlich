<?php
/*
 * abavo_form
 * 
 * @copyright   2017 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\Hooks;

use Abavo\AbavoForm\Domain\Service\SessionHookService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SessionHookExample is a light weight example to make a session hook for EXT:abavo_form
 *
 * Register your own hook via namespace like this
 * $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['sessionhooks'][] = [
 *   'label' => 'EXT:abavo_form - SessionHookExample',
 *   'value' => 'Abavo\AbavoForm\Hooks\SessionHookExample'
 * ];
 *
 * @author mbruckmoser
 */
class SessionHookExample extends SessionHookService
{

    /**
     * The main method
     */
    public function main(&$settings = [])
    {
        /*
         *  Place your code here to manipulate $this->formObj
         */

        // set processed
        $this->isProcessed = true;
        return $this;
    }
}