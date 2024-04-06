<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Abavo\AbavoSearch\User\AjaxResponse;
use Abavo\AbavoSearch\Domain\TermManager;
use Abavo\AbavoSearch\Domain\Repository\StatRepository;
use Abavo\AbavoSearch\Domain\Repository\IndexRepository;
use Abavo\AbavoSearch\Domain\Model\Stat;
use \Abavo\AbavoSearch\Domain\Model\AutocompleteItem;

class AjaxController extends ActionController
{
    /**
     *  AJAX responseHelper
     *
     * @var AjaxResponse
     */
    protected $ajaxResponseHelper;

    /**
     * termManager
     *
     * @var TermManager
     */
    protected $termManager;

    /**
     * statRepository
     *
     * @var StatRepository
     */
    protected $statRepository;

    /**
     * indexRepository
     *
     * @var IndexRepository
     */
    protected $indexRepository;

    /**
     * Autocomplete Action
     *
     * @param int $pid the storage pid
     * @param string $term the search term
     * @return string
     */
    public function autocompleteAction($pid = 0, $term = ''): ResponseInterface
    {
        $output = [];
        try {
            $results = $this->termManager->getTermsForAutocomplete(
                $term, $GLOBALS['TSFE']->sys_language_uid, $GLOBALS['TSFE']->fe_user->user['usergroup'], $pid, $this->settings['maxAutocompleteItems']
            );

            // Extract all 'search' columns, based on Term-Model-Query
            foreach (array_column($results, 'search') as $id => $result) {

                // Supporting AutocompleteItem objects
                if ($result instanceof AutocompleteItem) {
                    $result = $result->__toArray();
                }

                // Keep backward compatibel
                $resultLabel = $result['label'] ?? $result;

                // Create output item
                $output[$resultLabel] = [
                    'id' => $id,
                    'label' => $resultLabel // Using array because objects are cached and duplicated
                ];

                // Supporting direct search url
                if (isset($result['uri']) && strlen($result['uri'])) {
                    $output[$resultLabel]['uri'] = $result['uri'];
                } else {
                    $output[$resultLabel]['uri'] = $this->uriBuilder->setTargetPageUid($this->settings['targetPid'])
                        ->uriFor('search', ['search' => $result['term'] ?? $resultLabel], 'Search', $this->request->getControllerExtensionName(), 'piresults');
                }

                // Supporting advanced data
                if (isset($result['data'])) {
                    $output[$resultLabel]['data'] = $result['data'];
                }
            }

            if (!empty($output)) {
                ksort($output);
                $output = array_values($output);
            }
        } catch (\Exception $ex) {
            // in fault case do nothing
        }

        $this->view = $this->ajaxResponseHelper->getJsonView($this->controllerContext, $output);
        return $this->htmlResponse();
    }

    /**
     * Hit Index Action
     *
     * @return void
     */
    public function hitindexAction(): ResponseInterface
    {
        $output = [];
        $index  = null;

        try {

            $index = $this->indexRepository->findByUid($this->request->getArgument('uid'));

            // If index stat exist, increase hits value
            if (is_object($index)) {

                $stat = $this->statRepository->findOneByRefid($index->getRefid());
                if ($stat) {
                    $stat->setHits($stat->getHits() + 1);
                    $stat->setTstamp(time());
                    $this->statRepository->update($stat);
                } else {

                    $stat = new Stat;

                    $stat->setPid($index->getPid());
                    $stat->setType('record');
                    $stat->setRefid($index->getRefid());
                    $stat->setHits(1);
                    $stat->getSysLanguageUid();
                    $content = $index->getTitle()."\n".$index->getContent();
                    $stat->setVal(nl2br($content));
                    $stat->setTstamp(time());
                    $stat->setCrdate(time());

                    $this->statRepository->add($stat);
                }

                $output[] = ['hit_index' => $this->request->getArgument('uid')];
            }
        } catch (\Exception $ex) {
            $output = [
                'code' => $ex->getCode(),
                'file' => $ex->getFile(),
                'message' => $ex->getMessage(),
                'trace' => $ex->getTraceAsString()
            ];
        }

        $this->view = $this->ajaxResponseHelper->getJsonView($this->controllerContext, $output);
        return $this->htmlResponse();
    }

    public function injectAjaxResponseHelper(AjaxResponse $ajaxResponseHelper): void
    {
        $this->ajaxResponseHelper = $ajaxResponseHelper;
    }

    public function injectTermManager(TermManager $termManager): void
    {
        $this->termManager = $termManager;
    }

    public function injectStatRepository(StatRepository $statRepository): void
    {
        $this->statRepository = $statRepository;
    }

    public function injectIndexRepository(IndexRepository $indexRepository): void
    {
        $this->indexRepository = $indexRepository;
    }
}