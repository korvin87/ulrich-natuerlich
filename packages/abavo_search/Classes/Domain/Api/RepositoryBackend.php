<?php
/**
 * abavo_search - RepositoryBackend.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 08.06.2018 - 09:28:33
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Api;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * RepositoryBackend
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class RepositoryBackend
{
    public const DEFAULT_METHOD = 'findAll';

    /**
     * @var Repository
     */
    protected $repository = null;

    /**
     * @var string 
     */
    protected $method = null;

    /**
     * @var array 
     */
    protected $arguments = null;

    /**
     * @var ModifyItemInterface
     */
    protected $modifyItemClass = null;

    /**
     * the constructor
     * 
     * @param string $repositoryClassName
     * @param string $methodName
     * @param array $arguments
     * @param string $modifyItemClassName
     */
    public function __construct(string $repositoryClassName = null, string $methodName = null, array $arguments = null, string $modifyItemClassName = null)
    {
        if (is_subclass_of($repositoryClassName, Repository::class)) {
            $this->repository = GeneralUtility::makeInstance(ObjectManager::class)->get($repositoryClassName);
            if (method_exists($this->repository, $methodName)) {
                $this->method = $methodName;
            }
        }
        $this->arguments = $arguments;

        if ($modifyItemClassName !== null) {
            $class = new \ReflectionClass($modifyItemClassName);
            if ((boolean) $class->implementsInterface(ModifyItemInterface::class)) {
                $this->modifyItemClass = GeneralUtility::makeInstance($modifyItemClassName);
            }
        }
    }

    /**
     * get the repository
     * @return object
     */
    public function getRepository()
    {
        if (!is_object($this->repository)) {
            throw new \Exception('Repository object not set');
        }
        return $this->repository;
    }

    /**
     * get the method
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->method ?? self::DEFAULT_METHOD;
    }

    /**
     * get the arguments
     * 
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * get the modifyItemClass
     * @return object
     */
    public function getModifyItemClass()
    {
        return $this->modifyItemClass;
    }
}