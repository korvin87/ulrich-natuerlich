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
 * ViewHelper to decode JSON
 */
class JsonDecodeViewHelper extends AbstractViewHelper
{

    /**
     * Returns a decoded JSON array
     *
     * @return  array
     */
    public function render()
    {
        $data = $this->arguments['data'];
        return (array) json_decode($data, null, 512, JSON_THROW_ON_ERROR);
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('data', 'mixed', '', true);
    }
}
