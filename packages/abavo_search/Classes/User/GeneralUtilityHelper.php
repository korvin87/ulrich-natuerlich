<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\User;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * The GeneralUtilityHelper
 *
 * @author mbruckmoser
 */
class GeneralUtilityHelper implements SingletonInterface
{

    /**
     * removeXSS Recursive Method
     *
     * @param mixed $param
     * @return mixed
     */
    public static function removeXSSRecursive($param = null)
    {
        if ($param) {
            // Recursion if array
            if (is_array($param) || is_object($param)) {
                foreach ($param as $key => $item) {
                    $param[$key] = self::removeXSSRecursive($item);
                }
            } elseif (trim($param) !== '') {
                $param = $param;
            }
        }

        return $param;
    }

    /**
     * Converts bytes into human readable file size.
     * From http://php.net/manual/de/function.filesize.php#112996
     *
     * @param string $bytes
     * @return string human readable file size
     */
    static public function FileSizeConvert($bytes)
    {
        $result = null;
        $bytes   = floatval($bytes);
        $arBytes = [0 => ["UNIT" => "TB", "VALUE" => 1024 ** 4], 1 => ["UNIT" => "GB", "VALUE" => 1024 ** 3], 2 => ["UNIT" => "MB", "VALUE" => 1024 ** 2], 3 => ["UNIT" => "KB", "VALUE" => 1024], 4 => ["UNIT" => "B", "VALUE" => 1]];

        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem["VALUE"]) {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", ",", strval(round($result, 2)))." ".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }

    /**
     * Truncate a string
     * 
     * @param string $text
     * @param int $length
     * @param string $append
     * @return string
     */
    static public function truncateStringRespectWord($text, $length)
    {
        if (strlen($text) > $length) {

            $words = GeneralUtility::trimExplode(' ', $text, true);

            if (is_array($words) && !empty($words)) {
                if (count($words) === 1 && strlen(last($words)) > $length) {
                    $text = substr(last($words), 0, (int) $length);
                } else {

                    array_pop($words);
                    $text = implode(' ', $words);

                    if (strlen($text) > $length) {
                        $text = self::truncateStringRespectWord($text, $length);
                    }
                }
            }
        }

        return $text;
    }
}