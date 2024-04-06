<?php
/*
 * abavo_form
 *
 * @copyright   2016 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\Utility;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * FormSanitation
 *
 * @author mbruckmoser
 */
class FormSanitation implements SingletonInterface
{

    /**
     * Clean value / Remove XSS in array recursively
     *
     * @param mixed $value
     * @return mixed
     */
    public function cleanValueArrayRecursive($value = null)
    {

        // array usage
        if (is_array($value)) {
            foreach ($value as $key => $v) {

                if (is_array($v)) {
                    $value[$key] = $this->cleanValueArrayRecursive($v);
                } else {
                    $value[$key] = $this->cleanValue($v);
                }
            }
        } else {
            $value = $this->cleanValue($value);
        }

        return $value;
    }

    /**
     * Clean value / Remove XSS in a value
     *
     * @param mixed $value
     * @return mixed
     */
    public function cleanValue($value = null)
    {
        // remove XSS
        if ($value !== null && (!is_object($value) && !is_array($value) && !is_int($value) && !is_bool($value))) {
            $value = GeneralUtility::removeXSS($value);
        }

        return $value;
    }
}