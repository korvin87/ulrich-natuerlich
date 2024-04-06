<?php
/*
 * abavo_form
 *
 * @copyright   2017 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
/**
 * ViewHelper get domain model data
 *
 * @author mbruckmoser
 */
class RepositoryViewHelper extends AbstractViewHelper
{

    /**
     * Render Method
     *
     * @return boolean
     */
    public function render()
    {
        $namespace = $this->arguments['namespace'];
        $model = $this->arguments['model'];
        $param = $this->arguments['param'];
        $method = $this->arguments['method'];
        try {
            $return = null;

            // Get namespace
            $repositoryNamespacePath = "$namespace\\$model".'Repository';
            if (!class_exists($repositoryNamespacePath)) {
                throw new \Exception('Namespace and/or repository not exist');
            }

            // Init Repository
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $repository    = $objectManager->get($repositoryNamespacePath);
            $querySettings = $repository->createQuery()->getQuerySettings();
            $querySettings->setRespectStoragePage(false);
            $repository->setDefaultQuerySettings($querySettings);

            // Get records
            if ($method === '' || !method_exists($repository, $method)) {
                $return = $repository->findAll();
            } else {
                $return = $repository->{$method}($param);
            }

            return $return;
            //
        } catch (\Exception $ex) {
            if ($GLOBALS['BE_USER']->user) {
                DebuggerUtility::var_dump($ex, $ex->getMessage());
            }
        }
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('namespace', 'string', 'The repository´s namespace', false, '');
        $this->registerArgument('model', 'string', 'The model', false, '');
        $this->registerArgument('param', 'mixed', 'The uid of record, null returns all', false);
        $this->registerArgument('method', 'string', 'The repository method', false, '');
    }
}