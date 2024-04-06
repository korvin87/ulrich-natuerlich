<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Indexers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Abavo\AbavoSearch\Domain\Model\Index;
use Abavo\AbavoSearch\Domain\Model\Indexer;
use Abavo\AbavoSearch\Domain\Exception\IndexException;

/**
 * ExampleIndexer
 *
 * @author mbruckmoser
 */
class ExampleIndexer extends BaseIndexer
{
    /**
     * Should the index cleaned before?
     */
    public CONST DO_CLEAN_INDEX = true;

    /**
     * The constructor
     * 
     * @param array $settings
     */
    function __construct($settings)
    {
        $this->settings = $settings;
    }

    /**
     *
     * @param Indexer $indexer
     * @return array
     */
    public function getData(Indexer $indexer)
    {
        $resources = null;
        $timeStart = microtime(true);

        if (!$indexer) {
            throw new IndexException(__METHOD__.': No indexer given.');
        }

        /*
         *  STEP 1: DEFINE RESOURCE HERE
         *  ----------------------------
         *  $repository = \TYPO3\CMS\Extbase\Persistence\Repository::class
         *  $resources  = $repository->findByFooBar();
         *  $langUid    = -1;
         */

        if ($resources) {

            foreach ($resources as $result) {

                /*
                 * STEP 2: DEFINE YOUR INDEX DATA
                 */
                $title    = $result['title'];
                $content  = $result['content'];
                $abstract = $result['abstract'];

                // Index-Modify-Hook
                $this->modifyIndexHook($this->getTypeKey(), $result, $title, $content, $abstract);

                // Make Index Object
                $tempIndex = Index::getInstance();
                $tempIndex->setTitle(strip_tags($title));
                $tempIndex->setContent(preg_replace('!\s+!', ' ', strip_tags($content)));
                $tempIndex->setAbstract(preg_replace('!\s+!', ' ', strip_tags($abstract)));
                $tempIndex->setTarget($result['uid']);
                $tempIndex->setFegroup($result['fe_group'] ?: self::FE_GROUP_DEFAULT);
                $tempIndex->setSysLanguageUid($langUid);

                /*
                 * STEP 3: DEFINE REFERENCE TABLE
                 * $tempIndex->setRefid('example_table'.self::REFID_SEPERARTOR.$result['uid']);
                 */

                //Set additional fields
                $this->setAdditionalFields($tempIndex, $indexer);

                // Add index to list
                $this->data[] = $tempIndex;
            }
        }

        // Set duration time
        $this->duration[$indexer->getUid()] = (microtime(true) - $timeStart);

        return $this->data;
    }
}