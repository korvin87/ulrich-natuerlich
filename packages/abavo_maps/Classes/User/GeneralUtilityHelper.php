<?php
/*
 * abavo_maps
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace TYPO3\AbavoMaps\User;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * The GeneralUtilityHelper
 *
 * @author mbruckmoser
 */
class GeneralUtilityHelper implements SingletonInterface
{
    /*
     * Get mimic method from
     * http://php.net/manual/de/function.mysql-real-escape-string.php#101248
     */

    static public function mysql_escape_mimic($inp)
    {
        if (is_array($inp)) return array_map(__METHOD__, $inp);

        if (!empty($inp) && is_string($inp)) {
            return str_replace(['\\', "\0", "\n", "\r", "'", '"', "\x1a"], ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'], $inp);
        }

        return $inp;
    }
}