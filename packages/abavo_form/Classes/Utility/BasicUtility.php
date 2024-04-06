<?php
/*
 * abavo_form
 *
 * @copyright   2016 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\Utility;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\DomainObject\AbstractValueObject;
use TYPO3\CMS\Extbase\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * BasicUtility for lowlevel methods
 */
class BasicUtility implements SingletonInterface
{

    /**
     * Method to convert a \stdClass to a given domain model
     *
     * @param \stdClass $item
     * @param string $objectType
     * @return object
     */
    public function convertItemToDomainModel($item = null, $objectType = null)
    {
        if (($item instanceof \stdClass || is_array($item)) && $objectType != '') {
            $object = GeneralUtility::makeInstance($objectType);

            if ($object) {
                foreach ($item as $property => $value) {

                    if (property_exists($object, $property)) {
                        if (is_array($value)) {
                            foreach ($value as $key => $v) {
                                $value[$key] = (is_string($v) ? trim($v) : $v);
                            }
                        } else {
                            $value = trim($value);
                        }

                        $setter = 'set'.ucfirst($property);
                        $object->$setter($value);
                        $object->_memorizeCleanState($property);
                    }
                }

                // Force pid value
                if ($object->getPid() === null) {
                    $object->setPid(0);
                    $object->_memorizeCleanState('pid');
                }
            }

            return $object;
        }
    }

    /**
     * Convert Abstract Object To Array Method
     *
     * @param object $object
     * @return array
     */
    public function convertAbstractObjectToArray($object)
    {
        $convertedArray = null;
        try {
            if (is_object($object)) {

                // Get Lazy Objects Storage Objects
                if ($object instanceof LazyObjectStorage || $object instanceof ObjectStorage) {
                    $convertedArray = $object->toArray();
                } else {

                    // Get Lazy Loading Objects
                    if ($object instanceof LazyLoadingProxy) {
                        $object = $object->_loadRealInstance();
                    }

                    // Get Abstract Domain Object
                    if ($object instanceof AbstractDomainObject || $object instanceof AbstractEntity) {
                        $convertedArray = $object->_getCleanProperties();
                    }

                    // Get Abstract Domain Value Object
                    if ($object instanceof AbstractValueObject) {
                        $convertedArray = $object->_getProperties();
                    }

                    // Get abavo ObjectStroage
                    if ($object instanceof \Abavo\AbavoForm\Domain\DataHandling\ObjectStorage) {
                        $convertedArray = $object->getArrayCopy();
                    }

                    // Get stdClass object
                    if ($object instanceof \stdClass) {
                        $convertedArray = get_object_vars($object);
                    }

                    // Fallback: convert other types
                    if (empty($convertedArray)) {
                        $convertedArray = ArrayUtility::convertObjectToArray($object);
                    }

                    // Add object´s class for back converting possibility
                    if (!empty($convertedArray)) {
                        $convertedArray['__CLASS__'] = get_class($object);
                    }
                }

                // check array for object types
                if (is_array($convertedArray) && !empty($convertedArray)) {

                    array_walk($convertedArray,
                        function(&$value, &$key) {
                        if (is_object($value)) {
                            $value = $this->convertAbstractObjectToArray($value);
                        }
                    });
                }
            }
        } catch (\Exception $ex) {
            if ($GLOBALS['BE_USER']->user) {
                DebugUtility::debugInPopUpWindow($ex->getTraceAsString());
            }
        }
        return $convertedArray;
    }

    /**
     * Combine 2 arrays method
     *
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    public function array_combine2($arr1, $arr2)
    {
        $count = min(count($arr1), count($arr2));
        return array_combine(array_slice($arr1, 0, $count), array_slice($arr2, 0, $count));
    }

    /**
     * Normalize string method
     * - convert all special chars like german umlauts
     * - remove all special chars are not A-Za-z0-9
     *
     * @param string $string
     * @return string
     */
    public function normalizeString($string)
    {
        $normalizeChars = ['Š' => 'S', 'š' => 's', 'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f', 'ă' => 'a', 'î' => 'i', 'â' => 'a', 'ș' => 's', 'ț' => 't', 'Ă' => 'A', 'Î' => 'I', 'Â' => 'A', 'Ș' => 'S', 'Ț' => 'T', 'Ä' => 'Ae', 'ä' => 'ae', 'Ö' => 'Oe', 'ö' => 'oe', 'Ü' => 'Ue', 'ü' => 'ue'];

        $string = strtr($string, $normalizeChars);
        $string = trim(preg_replace('/[^A-Za-z0-9]/', '', $string));

        return $string;
    }

    /**
     * Check if string is a valid md5 hash
     * 
     * @param type $md5
     * @return type
     */
    public function isValidMd5($md5 = '')
    {
        return strlen($md5) == 32 && ctype_xdigit($md5);
    }

    /**
     * Get post_max_size in bytes
     * Forked from http://php.net/manual/en/function.ini-get.php
     *
     * @param string $val
     * @return int
     */
    public function returnBytes($val)
    {
        $val  = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    /**
     * clean input array for HTML recursive
     * 
     * @param array $data
     * @return array
     */
    public function cleanInputArrayForHtmlRecursive($data)
    {
        if (is_array($data) && !empty($data)) {
            foreach ($data as $key => $item) {
                if (is_array($item)) {
                    $data[$key] = self::cleanInputArrayForHtmlRecursive($data);
                } else {
                    $data[$key] = htmlentities($item);
                }
            }
        }

        return $data;
    }

    /**
     * Get an object by uid
     * @param int $uid The uid
     * @param string $object The objectName
     * @return mixed Object from class of $object | NULL if not found
     */
    public function getPersistentObject($uid, $object)
    {
        if (class_exists($object)) {
            $repositoryName = str_replace('Model', 'Repository', $object).'Repository';
            if (class_exists($repositoryName)) {
                /* @var $repository \TYPO3\CMS\Extbase\Persistence\Repository */
                $repository = GeneralUtility::makeInstance(ObjectManager::class)->get($repositoryName);
                return $repository->findByUid($uid);
            }
        }
        return NULL;
    }
}