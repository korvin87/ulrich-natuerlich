<?php

namespace Abavo\AbavoForm\Domain\DataHandling;

/*
 * ObjectStorage class
 * 
 * @copyright   2016 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ObjectStorage
 *
 * @author mbruckmoser
 */
class ObjectStorage extends \ArrayObject
{

    /**
     * Apend a value with unique identifier
     *
     * @param mixed $object
     * @param string $identifier (optional)
     * @param bool $forceOverwrite (optional) overwrite existing value
     * @throws \Exception
     */
    public function append($object = null, $identifier = null, $forceOverwrite = false)
    {
        // Set identifier
        if ($identifier === null) {
            if (method_exists($object, 'getIdentifier')) {
                $identifier = $object->getIdentifier();
            } else {
                if (method_exists($object, 'getIdentifierHash')) {
                    $identifier = $object->getIdentifierHash();
                } else {
                    $identifier = spl_object_hash($object);
                }
            }
        }

        // Overwrite $opject
        if ($forceOverwrite === false && self::offsetExists($identifier)) {
            throw new \Exception('Object in storage already exist. Use param "forceOverwrite=true" to overwrite.');
        }

        // Set $object
        self::offsetSet($identifier, $object);
    }

    /**
     * Append a array with objects
     *
     * @param array $array
     */
    public function appendArray($array = [])
    {
        if (!empty($array)) {
            foreach ($array as $object) {
                if (is_object($object)) {
                    $this->append($object);
                }
            }
        }
    }

    /**
     * Remoge a value by identifer/key
     *
     * @param string $identifier
     */
    public function remove($identifier = null)
    {
        $this->offsetUnset($identifier);
    }
}