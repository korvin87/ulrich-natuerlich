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
 * DomainModel Page
 *
 * @author mbruckmoser
 */
class Page extends AbstractEntity
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
     * title
     * @var string
     *
     */
    protected $title;

    /**
     * subtitle
     * @var string
     *
     */
    protected $subtitle;

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
    protected $fegroup = '';

    /**
     * categories
     *
     * @var ObjectStorage<Category>
     * @Extbase\ORM\Lazy
     */
    protected $categories = null;

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
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns the subtitle
     *
     * @return string $subtitle
     */
    public function getSubtitle()
    {
        return $this->subtitle;
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
     * Returns the fegroup
     *
     * @return string $abstract
     */
    public function getFegroup()
    {
        return $this->fegroup;
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