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
 * ViewHelper to render promise script
 */
class PromiseViewHelper extends AbstractViewHelper
{

    /**
     * Parse content element
     *
     * @return  string	content element
     */
    public function render()
    {
        $data = $this->arguments['data'];
        $mapuid = $this->arguments['mapuid'];
        $content       = '';
        $anyPromise    = false;
        $successScript = 'centerMapToBounds('.$mapuid.');';
        foreach ($data as $marker) {
            if ($marker->_getProperty('longitude') == '' || $marker->_getProperty('latitude') == '') {
                $anyPromise = true;
                $content .= ', promise'.$marker->_getProperty('uid');
            }
        }
        // Return content
        if ($anyPromise) {
            return '$.when('.substr($content, 1).' ).done(function() { '.$successScript.' });';
        } else {
            return $successScript;
        }
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('data', 'object', 'marker opjects', true);
        $this->registerArgument('mapuid', 'int', 'the mapuid', true);
    }
}
