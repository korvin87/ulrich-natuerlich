<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * DomainModel Index
 *
 * @author mbruckmoser
 */
class Index extends AbstractEntity
{
    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * content
     *
     * @var string
     */
    protected $content = '';

    /**
     * params
     *
     * @var string
     */
    protected $params = '';

    /**
     * target
     *
     * @var string
     */
    protected $target = '';

    /**
     * filereference
     *
     * @var ObjectStorage<FileReference>
     * @Extbase\ORM\Lazy
     */
    protected $filereference = null;

    /**
     * indexer
     *
     * @var ObjectStorage<Indexer>
     * @Extbase\ORM\Lazy
     */
    protected $indexer = null;

    /**
     * refid
     *
     * @var string
     */
    protected $refid = '';

    /**
     * abstract
     *
     * @var string
     */
    protected $abstract = '';

    /**
     * fegroup
     *
     * @var string
     */
    protected $fegroup = '0';

    /**
     * datetime
     *
     * @var string
     */
    protected $datetime = null;

    /**
     * sysLanguageUid
     *
     * @var int
     */
    protected $sysLanguageUid = 0;

    /**
     * categories
     *
     * @var string
     */
    protected $categories = '';

    /**
     * ranking
     *
     * @var int
     */
    protected $ranking = 0;

    /**
     * hits
     *
     * @var int
     */
    protected $hits = 0;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Get a instance of this class
     * 
     * @return \Abavo\AbavoSearch\Domain\Model\Index
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(static::class);
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->indexer       = new ObjectStorage();
        $this->filereference = new ObjectStorage();
    }

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Returns the content
     *
     * @return string $content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Returns the params
     *
     * @return string $params
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Sets the params
     *
     * @param string $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Returns the target
     *
     * @return string $target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Sets the target
     *
     * @param string $target
     * @return $this
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Returns the filereference
     *
     * @return void
     */
    public function getFilereference()
    {
        if ($this->target) {
            $fac     = GeneralUtility::makeInstance(ResourceFactory::class);
            $fileRef = null;

            [$storageId, $objectIdentifier] = GeneralUtility::trimExplode(':', $this->target);
            $storage = $fac->getStorageObject($storageId);

            if ($storage->hasFile($objectIdentifier)) {
                $fileRef = $storage->getFile($objectIdentifier);
            }

            return $fileRef;
        }
    }

    /**
     * Returns the indexer
     *
     * @return ObjectStorage<Indexer> $indexer
     */
    public function getIndexer()
    {
        return $this->indexer;
    }

    /**
     * Sets the indexer
     *
     * @param ObjectStorage<Indexer> $indexer
     * @return $this
     */
    public function setIndexer($indexer)
    {
        $this->indexer = $indexer;
        return $this;
    }

    /**
     * Returns the abstract
     *
     * @return string $abstract
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * Sets the abstract
     *
     * @param string $abstract
     * @return $this
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
        return $this;
    }

    /**
     * Returns the fegroup
     *
     * @return string $fegroup
     */
    public function getFegroup()
    {
        return $this->fegroup;
    }

    /**
     * Sets the fegroup
     *
     * @param string $fegroup
     * @return $this
     */
    public function setFegroup($fegroup)
    {
        $this->fegroup = $fegroup;
        return $this;
    }

    /**
     * Returns the datetime
     *
     * @return \DateTime $datetime
     */
    public function getDatetime()
    {
        return($this->datetime instanceof \DateTime) ? $this->datetime : \DateTime::createFromFormat('Y-m-d H:i:s', $this->datetime);
    }

    /**
     * Sets the Datetime
     *
     * @param \DateTime $datetime
     * @return $this
     */
    public function setDatetime($datetime)
    {
        $this->datetime = ($datetime instanceof \DateTime) ? $datetime->format('Y-m-d H:i:s') : $datetime;
        return $this;
    }

    /**
     * Returns the sysLanguageUid
     *
     * @return int $sysLanguageUid
     */
    public function getSysLanguageUid()
    {
        return $this->sysLanguageUid;
    }

    /**
     * Sets the sysLanguageUid
     *
     * @param int $sysLanguageUid
     * @return $this
     */
    public function setSysLanguageUid($sysLanguageUid)
    {
        $this->sysLanguageUid = $sysLanguageUid;
        return $this;
    }

    /**
     * Returns the categories
     *
     * @return string $categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Sets the categories
     *
     * @param string $categories
     * @return $this
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * Returns the ranking
     *
     * @return float $ranking
     */
    public function getRanking()
    {
        return $this->ranking;
    }

    /**
     * Sets the ranking
     *
     * @param float $ranking
     * @return $this
     */
    public function setRanking($ranking)
    {
        $this->ranking = $ranking;
        return $this;
    }

    /**
     * Returns the refid
     *
     * @return string $refid
     */
    public function getRefid()
    {
        return $this->refid;
    }

    /**
     * Sets the refid
     *
     * @param string $refid
     * @return $this
     */
    public function setRefid($refid)
    {
        $this->refid = $refid;
        return $this;
    }

    /**
     * Returns the hits
     *
     * @return int $hits
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Sets the hits
     *
     * @param int $hits
     * @return $this
     */
    public function setHits($hits)
    {
        $this->hits = $hits;
        return $this;
    }
}