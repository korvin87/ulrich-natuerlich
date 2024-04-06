<?php
/*
 * abavo_search
 * 
 * @copyright   2017 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Service;

use Abavo\AbavoSearch\Domain\Repository\IndexRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SearchService
 *
 * @author mbruckmoser
 */
class IndexSearchService extends SearchService
{
    /**
     * indexRepository
     *
     * @var IndexRepository
     */
    protected $indexRepository;

    /**
     * Constructor
     *
     * @param array $settings
     */
    public function __construct($settings = [], $searchConditions = [])
    {
        parent::__construct($settings, $searchConditions);        

        if (empty($this->settings)) {
            throw new \Exception('No settings for '.self::class.' given.');
        }

        // define basic settings
        $this->indexRepository = $this->objectManager->get(IndexRepository::class);
        $this->indexRepository->setDefaultQuerySettings(
            $this->indexRepository->createQuery()->getQuerySettings()->setStoragePageIds(GeneralUtility::intExplode(',', $this->settings['storagePids']))
        );
        $this->isInitialized = true;

        return $this;
    }

    /**
     * Find for search service method
     *
     * @param array $searchConditions the search conditions
     * @return array
     * @throws \Exception
     */
    public function findForSearchService($searchConditions = [], $structure = [])
    {
        $result = $this->indexRepository->findForSearch(
            $searchConditions['search'],
            $searchConditions['start'],
            $searchConditions['length'],
            $searchConditions['L'],
            $searchConditions['feGroup'],
            $searchConditions['categories'],
            $searchConditions['facets'],
            $searchConditions['orderBy'],
            $searchConditions['sorting'],
            $structure
        );
        
        return $result;
    }
}