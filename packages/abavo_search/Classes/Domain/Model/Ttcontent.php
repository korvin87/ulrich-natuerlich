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
use TYPO3\CMS\Extbase\Domain\Model\Category;
/**
 * DomainModel Ttcontent
 *
 * @author mbruckmoser
 */
class Ttcontent extends AbstractEntity
{
    /**
     * uid
     * @var int
     * @Extbase\Validate("NotEmpty")
     */
    protected $uid;

    /**
     * pid
     * @var int
     * @Extbase\Validate("NotEmpty")
     */
    protected $pid;

    /**
     * sorting
     * @var int
     * @Extbase\Validate("NotEmpty")
     */
    protected $sorting;

    /**
     * header
     * @var string
     *
     */
    protected $header;

    /**
     * headerLink
     * @var string
     *
     */
    protected $headerLink;

    /**
     * bodytext
     * @var string
     *
     */
    protected $bodytext;

    /**
     * image
     * @var string
     *
     */
    protected $image;

    /**
     * imageLink
     * @var string
     *
     */
    protected $imageLink;

    /**
     * colPos
     * @var int
     *
     */
    protected $colPos;

    /**
     * categories
     *
     * @var ObjectStorage<Category>
     * @Extbase\ORM\Lazy
     */
    protected $categories = null;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
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
        $this->categories = new ObjectStorage();
    }

    /**
     * Returns the pid
     *
     * @return int $pid
     */
    public function getPid(): ?int
    {
        return $this->pid;
    }

    /**
     * Returns the sorting
     *
     * @return int $sorting
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Returns the header
     *
     * @return string $header
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Returns the headerLink
     *
     * @return string $headerLink
     */
    public function getHeaderLink()
    {
        return $this->headerLink;
    }

    /**
     * Returns the bodytext
     *
     * @return string $bodytext
     */
    public function getBodytext()
    {
        return $this->bodytext;
    }

    /**
     * Returns the image
     *
     * @return string $image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Returns the imageLink
     *
     * @return string $imageLink
     */
    public function getImageLink()
    {
        return $this->imageLink;
    }

    /**
     * Returns the colPos
     *
     * @return int $colPos
     */
    public function getColPos()
    {
        return $this->colPos;
    }

    /**
     * Returns the categories
     *
     * @return ObjectStorage<Category> $categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Sets the categories
     *
     * @param ObjectStorage<Category> $categories
     * @return void
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }
}