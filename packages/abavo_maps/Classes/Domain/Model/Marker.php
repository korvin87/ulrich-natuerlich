<?php

namespace TYPO3\AbavoMaps\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\AbavoMaps\Domain\Repository\MarkerRepository;
/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
/**
 * Ein Marker einer Karte
 */
class Marker extends AbstractEntity
{
    /**
     * Titel
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected $title = '';

    /**
     * Breitengrad
     *
     * @var string
     */
    protected $latitude = '';

    /**
     * Längengrad
     *
     * @var string
     */
    protected $longitude = '';

    /**
     * Vollständige Adresse
     *
     * @var string
     */
    protected $fulladdress = '';

    /**
     * Beschreibung
     *
     * @var string
     */
    protected $description = '';

    /**
     * Marker Link (optional: If set, don´t show description on clicking marker and
     * refer to link target)
     *
     * @var string
     */
    protected $url = '';

    /**
  * categories
  *
  * @var ObjectStorage<Category>
  * @Extbase\ORM\Lazy
  */
 protected $categories = null;

    /**
     * Marker Icon (optional)
     *
     * @var ObjectStorage<FileReference>
     */
    protected $pinicon;

    /**
     * Domain model repository name
     *
     * @var string
     */
    protected $repo = MarkerRepository::class;

    /**
     * __construct
     *
     * @return Googlemap
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties.
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        /**
         * Do not modify this method!
         * It will be rewritten on each save in the extension builder
         * You may modify the constructor of this class instead
         */
        $this->categories = new ObjectStorage();
        $this->pinicon = new ObjectStorage();
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
    }

    /**
     * Returns the latitude
     *
     * @return string $latitude
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Returns domain model repository name
     *
     * @return string model
     */
    public function getRepo()
    {
        return base64_encode($this->repo);
    }

    /**
     * Sets the latitude
     *
     * @param string $latitude
     * @return void
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * Returns the longitude
     *
     * @return string $longitude
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Sets the longitude
     *
     * @param string $longitude
     * @return void
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * Returns the fulladdress
     *
     * @return string $fulladdress
     */
    public function getFulladdress()
    {
        return $this->fulladdress;
    }

    /**
     * Sets the fulladdress
     *
     * @param string $fulladdress
     * @return void
     */
    public function setFulladdress($fulladdress)
    {
        $this->fulladdress = $fulladdress;
    }

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the url
     *
     * @return string $url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the url
     *
     * @param string $url
     * @return void
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
  * Returns the categories
  *
  * @return ObjectStorage<Category> $categories
  */
 public function getCategories() {
		return $this->categories;
	}

	/**
  * Sets the categories
  *
  * @param ObjectStorage<Category> $categories
  * @return void
  */
 public function setCategories($categories) {
		$this->categories = $categories;
	}

    /**
     * Returns the pinicon
     *
     * @return integer $pinicon
     */
    public function getPinicon()
    {
        return $this->pinicon;
    }

    /**
     * Sets the pinicon
     *
     * @param integer $pinicon
     * @return void
     */
    public function setPinicon($pinicon)
    {
        $this->pinicon = $pinicon;
    }
}