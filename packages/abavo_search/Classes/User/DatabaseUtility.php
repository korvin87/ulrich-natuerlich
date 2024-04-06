<?php
/*
 * abavo_search
 *
 * @copyright   2017 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\User;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Exception;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 *  DatabaseUtility
 *
 * @author mbruckmoser
 */
class DatabaseUtility implements SingletonInterface
{

    /**
     * This Method was taken and forked from /typo3/sysext/extbase/Classes/Persistence/Generic/Storage/Typo3DbBackend.php
     * For buggy prepared statements of TYPO3 Version 7.2.0 https://forge.typo3.org/issues/67375
     * ------------------------------------------------------------------------------------------------------------------
     * Replace query placeholders in a query part by the given
     * parameters.
     *
     * @param string &$sqlString The query part with placeholders
     * @param array $parameters The parameters
     *
     * @throws Exception
     */
    public static function replacePlaceholders(&$sqlString, array $parameters)
    {
        if (substr_count($sqlString, '?') !== count($parameters)) {
            DebuggerUtility::var_dump(['SQL' => $sqlString, 'params' => $parameters], 'replace');
            throw new Exception('The number of question marks to replace must be equal to the number of parameters.', 1_433_923_990);
        }

        // Get MySQL connection resourcehandle
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionByName('Default');
        $mysqli     = $connection->getWrappedConnection()->getWrappedResourceHandle();

        $offset = 0;
        foreach ($parameters as $parameter) {
            $markPosition = strpos($sqlString, '?', $offset);
            if ($markPosition !== FALSE) {
                if ($parameter === NULL) {
                    $parameter = 'NULL';
                } elseif (is_array($parameter) || $parameter instanceof \ArrayAccess || $parameter instanceof \Traversable) {
                    $items = [];
                    foreach ($parameter as $item) {
                        $items[] = $mysqli->real_escape_string($item);
                    }
                    $parameter = '('.implode(',', $items).')';
                } else {
                    $parameter = "'".$mysqli->real_escape_string($parameter)."'";
                }
                $sqlString = substr($sqlString, 0, $markPosition).$parameter.substr($sqlString, ($markPosition + 1));
            }
            $offset = $markPosition + strlen($parameter);
        }
    }

    /**
     * Get mimic method from
     * http://php.net/manual/de/function.mysql-real-escape-string.php#101248
     */
    static public function mysqlEscapeMimic($inp)
    {
        if (is_array($inp)) {
            return array_map(__METHOD__, $inp);
        }

        if (!empty($inp) && is_string($inp)) {
            return str_replace(['\\', "\0", "\n", "\r", "'", '"', "\x1a"], ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'], $inp);
        }

        return $inp;
    }
}