<?php
/**
 * ulrich_products - GetInstanceStaticTrait.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 05.06.2018 - 08:05:30
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * GetInstanceStaticTrait
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
trait GetInstanceStaticTrait
{

    /**
     * Get a instance of this class
     * 
     * @return static::class
     */
    public static function getInstance($arguments = [])
    {
        $hasObjectManagerInterface = false;

        // check if class need objectManager
        $class       = new \ReflectionClass(static::class);
        if ($constructor = $class->getConstructor()) {
            if ($constructor->getParameters()) {
                foreach ($constructor->getParameters() as $parameter) {
                    if ($parameter instanceof \ReflectionParameter && $parameter->getClass() instanceof \ReflectionClass && $parameter->getClass()->getName() === ObjectManagerInterface::class) {
                        $hasObjectManagerInterface = true;
                        break;
                    }
                }
            }
        }

        // MakeInstance
        if ($hasObjectManagerInterface) {
            $instance = GeneralUtility::makeInstance(ObjectManager::class)->get(static::class);
        } else {
            $instance = call_user_func_array([GeneralUtility::class, 'makeInstance'], array_merge([static::class], $arguments));
        }
        return $instance;
    }
}