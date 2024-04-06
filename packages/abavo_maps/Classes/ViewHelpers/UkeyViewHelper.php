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
 * ViewHelper to render a Ukey
 */
class UkeyViewHelper extends AbstractViewHelper
{

    /**
     * Generate a Ukey
     *
     * @return  string	<uid>/encryptionKey@<tablename>/encryptionKey
     */
    public function render()
    {
        $data = $this->arguments['data'];
        return md5($data.'/'.$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('data', 'mixed', '', true);
    }
}
