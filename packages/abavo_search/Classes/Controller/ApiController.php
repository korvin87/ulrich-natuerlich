<?php
/**
 * abavo_search - ApiController.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 07.06.2018 - 13:46:03
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\AbavoSearch\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Abavo\AbavoSearch\Domain\Api\ConfigurationInterface;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use Abavo\AbavoSearch\Domain\Model\Indexer;
use Abavo\AbavoSearch\Domain\Api\Utility;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository as ExtbaseRepository;
use Abavo\AbavoSearch\Domain\Api;

/**
 * ApiController
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class ApiController extends ActionController
{
    public const STATE_NOTICE  = -2;
    public const STATE_INFO    = -1;
    public const STATE_OK      = 0;
    public const STATE_WARNING = 1;
    public const STATE_ERROR   = 2;

    /**
     * Variables that should be assigned in view
     * @var array
     */
    protected $variables = ['data' => ['items' => [], 'jsonViewConfigurationClass' => null], 'message' => '', 'status' => self::STATE_INFO];

    /**
     * The JsonView configuration
     * @var ConfigurationInterface
     */
    protected $jsonViewConfiguration = null;

    /**
     * initialize Action
     */
    protected function initializeAction()
    {
        parent::initializeAction();
        $this->defaultViewObjectName = JsonView::class;
    }

    /**
     * action indexer
     *
     * @param Indexer $indexer
     * @return void
     */
    public function indexerAction(Indexer $indexer): ResponseInterface
    {
        try {
            // Init jsonView configuration
            $indexerSettings = $indexer->getConfig()['settings'] ?? null;
            if (isset($indexerSettings['jsonViewConfigurationClass']) && Utility::isJsonViewConfigurationClassValid($indexerSettings['jsonViewConfigurationClass'])) {
                $this->jsonViewConfiguration = GeneralUtility::makeInstance($indexerSettings['jsonViewConfigurationClass']);
            }

            if ($this->jsonViewConfiguration === null) {
                throw new \Exception('The JsonView configuration could not be instantiated.');
            } else {
                $this->variables['data']['jsonViewConfigurationClass'] = $indexerSettings['jsonViewConfigurationClass'];
            }

            // Set JsonView configuration
            $this->view->setConfiguration([
                'data' => [
                    'items' => [
                        '_descendAll' => $this->jsonViewConfiguration->getJsonViewConfiguration()
                    ]
                ]
            ]);

            // Initialize repository
            if (($repositoryBackend = $this->jsonViewConfiguration->getRepositoryBackend())) {
                if (($repository = $repositoryBackend->getRepository()) instanceof ExtbaseRepository && isset($indexerSettings['pages'])) {
                    $repository->setDefaultQuerySettings($repository->createQuery()->getQuerySettings()->setStoragePageIds(GeneralUtility::intExplode(',', $indexerSettings['pages'])));
                }

                // Get records
                if ($records = $repository->{$repositoryBackend->getMethod()}($repositoryBackend->getArguments() ?? null)) {

                    $items = ($records instanceof QueryResult) ? $records->toArray() : $records;

                    // modifications here?
                    if ($modifyItemInstance = $repositoryBackend->getModifyItemClass()) {
                        array_walk($items, function($item) use (&$items, $modifyItemInstance) {
                            $modifyItemInstance->modfiyItem($item);
                        });
                    }

                    // Set results
                    $this->variables['data']['items'] = $items;
                    $this->variables['status']        = self::STATE_OK;
                }
            } else {
                throw new \Exception('Repository of JsonView configuration could not be intialized.');
            }
            //
        } catch (\Exception $ex) {
            $this->variables['message'] = $ex->getMessage();
            $this->variables['status']  = self::STATE_ERROR;
            $this->response->setStatus(500);
        }

        // Assign data to view
        $this->controllerContext->getResponse()->setHeader('Content-Type', 'application/json; charset=utf-8');
        $this->view->setVariablesToRender(array_keys($this->variables));
        $this->view->assignMultiple($this->variables);
        return $this->htmlResponse();
    }
}