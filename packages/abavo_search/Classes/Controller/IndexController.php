<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Abavo\AbavoSearch\Domain\Repository\IndexRepository;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use Abavo\AbavoSearch\Domain\Model\Indexer;
use Abavo\AbavoSearch\Domain\Exception\IndexException;

/**
 * IndexController
 *
 * @author mbruckmoser
 */
class IndexController extends ActionController
{
    /**
     * indexerRepository
     *
     * @var IndexRepository
     */
    protected $indexRepository;

    /**
     *
     * @param array $data
     * @return bool
     */
    public function createIndexCollectionForCommandController(array $indexCollection = [])
    {
        try {
            if (empty($indexCollection)) {
                throw new IndexException('createIndexCollectionForCommandController: No indexCollection');
            }

            foreach ($indexCollection as $index) {
                $this->indexRepository->add($index);
            }

            return true;
        } catch (\Exception $e) {
            DebuggerUtility::var_dump($e->getMessage(), 'abavo_search Error');
            return false;
        }
    }

    /**
     *
     * @param Indexer $indexer
     * @return boolean
     */
    public function cleanIndexForCommandController(Indexer $indexer)
    {
        try {
            $this->indexRepository->cleanUpByIndexer($indexer);
            return true;

        } catch (\Exception $e) {
            DebuggerUtility::var_dump($e->getMessage(), 'abavo_search Error');
            return false;
        }
    }

    /**
     * @return boolean
     */
    public function cleanUnusedIndexForCommandController($pid = '0', $uids = [])
    {
        try {
            $this->indexRepository->cleanUpByUnuesedIndex($pid, $uids);
            return true;

        } catch (\Exception $e) {
            DebuggerUtility::var_dump($e->getMessage(), 'abavo_search Error');
            return false;
        }
    }

    public function injectIndexRepository(IndexRepository $indexRepository): void
    {
        $this->indexRepository = $indexRepository;
    }
}