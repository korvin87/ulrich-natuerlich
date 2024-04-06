<?php
/*
 * abavo_search
 *
 * @copyright   2017 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
/**
 * DomainModel ServiceIndex
 *
 * This class is used be registered \Abavo\AbavoSearch\Domain\Service\SearchService
 *
 * @author mbruckmoser
 */
class SearchServiceIndex extends Index
{
    /**
     * additionalInfo
     *
     * @var array
     */
    protected $additionalInfo = [];

    /**
     * Constructor
     *
     * @param type $indexerTitle
     */
    public function __construct($indexerTitle = 'SearchService')
    {
        parent::__construct();
        $indexer       = GeneralUtility::makeInstance(Indexer::class);
        $indexer->setTitle('Transient indexer for SearchService classes');
        $indexer->setType($indexerTitle);
        $objectStorage = GeneralUtility::makeInstance(ObjectStorage::class);
        $objectStorage->attach($indexer);
        $this->setIndexer($objectStorage);
    }

    /**
     * Returns the additionalInfo
     *
     * @return array $additionalInfo
     */
    public function getAdditionalInfo()
    {
        return $this->additionalInfo;
    }

    /**
     * Sets the additionalInfo
     *
     * @param array $additionalInfo
     * @return void
     */
    public function setAdditionalInfo($additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;
    }
}