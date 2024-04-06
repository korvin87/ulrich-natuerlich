<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Indexers;

use TYPO3\CMS\Core\SingletonInterface;
use Abavo\AbavoSearch\Domain\Model\Indexer;
use Abavo\AbavoSearch\Domain\Model\Index;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Abavo\AbavoSearch\Domain\Exception\IndexException;

/**
 * BaseIndexer
 *
 * @author mbruckmoser
 */
class BaseIndexer implements SingletonInterface
{
    public const REFID_SEPERARTOR = '|';
    public const FE_GROUP_DEFAULT = '0';

    /**
     * data
     *
     * @var array
     */
    protected $data = [];

    /**
     * settings
     *
     * @var array
     */
    protected $settings = [];

    /**
     * duration
     *
     * @var array
     */
    protected $duration = [];

    /**
     * Constructor
     * 
     * @param array $settings
     */
    function __construct($settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get a instance of this class
     * 
     * @param array $settings
     * @return \Abavo\AbavoSearch\Indexers\BaseIndexer
     */
    public static function getInstance($settings = [])
    {
        return GeneralUtility::makeInstance(static::class, $settings);
    }

    /**
     *
     * @param Indexer $indexer
     * @return array
     */
    public function getData(Indexer $indexer)
    {
        $timeStart = microtime(true);

        // Set duration time
        $this->duration[$indexer->getUid()] = (microtime(true) - $timeStart);

        return $this->data;
    }

    /**
     * Get the indexer typeKey
     * 
     * @return type
     */
    public function getTypeKey()
    {
        return str_replace('Indexer', '', basename(__FILE__, '.php'));
    }

    /**
     *
     * @param Indexer $indexer
     * @return array
     */
    public function getDuration(Indexer $indexer)
    {
        return $this->duration[$indexer->getUid()];
    }

    /**
     * Modify Index Hook
     *
     * @param string $typeKey in $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['modifyIndex'][$typeKey]
     * @param object $recordObj The Record Object
     * @param string $title The index title
     * @param string $content The index content
     * @param string $abstract The index abstract
     */
    public function modifyIndexHook($typeKey = '', &$recordObj, &$title, &$content, &$abstract)
    {
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['modifyIndex'][$typeKey])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['modifyIndex'][$typeKey] as $_classRef) {
                $_procObj = GeneralUtility::makeInstance($_classRef);
                if (!method_exists($_classRef, 'modifyIndex')) {
                    throw new IndexException('BaseIndexer\modifyIndexHook: No modifyIndex method in '.$_classRef.' defined.');
                }
                $_procObj->modifyIndex($recordObj, $title, $abstract, $content);
            }
        }
    }

    /**
     * Set Additional Fields
     *
     * @param Index $tempIndex
     * @param Indexer $indexer
     * @return array
     */
    public function setAdditionalFields(Index &$tempIndex, Indexer $indexer)
    {
        $tempIndex->setIndexer($indexer->getUid());
        $tempIndex->setPid($indexer->getStoragepid());
        $tempIndex->setDateTime(new \DateTime);

        // Merge categories from index and indexerConfig
        $categories = trim($tempIndex->getCategories());
        if ((boolean) $indexer->getCategories()) {
            $categories = ($categories == '') ? $indexer->getCategories() : $categories.','.$indexer->getCategories();
        }
        $tempIndex->setCategories($categories);
    }
}