<?php

namespace TYPO3\AbavoMaps\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\AbavoMaps\Domain\Repository\AddressRepository;
/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
/**
 * Ein Addresse als Marker einer Karte
 */
class Address extends AbstractEntity
{
    /**
     * Name
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected $name = '';

    /**
     * Firstname
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected $firstname = '';

    /**
     * Lastname
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected $lastname = '';

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
     * Adresse
     *
     * @var string
     */
    protected $address = '';

    /**
     * Vollständige Adresse
     *
     * @var string
     */
    protected $fulladdress = '';

    /**
     * Stadt
     *
     * @var string
     */
    protected $city = '';

    /**
     * PLZ
     *
     * @var string
     */
    protected $zip = '';

    /**
     * Country
     *
     * @var string
     */
    protected $country = '';

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
     * Domain model repository name
     *
     * @var string
     */
    protected $repo = AddressRepository::class;

    /**
     * Returns the name
     *
     * @return string $title
     */
    public function getName()
    {
        return ( $this->name != '' ) ? $this->name : $this->firstname.' '.$this->lastname;
    }

    /**
     * Sets the name
     *
     * @param string $title
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the firstname
     *
     * @return string $firstname
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Sets the firstname
     *
     * @param string $firstname
     * @return void
     */
    public function setFirstname($firstname)
    {
        $this->title = $firstname;
    }

    /**
     * Returns the lastname
     *
     * @return string $lastname
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Sets the lastname
     *
     * @param string $lastname
     * @return void
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
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
        return $this->address.' '.$this->zip.' '.$this->city.' '.$this->country;
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
     * Returns the zip
     *
     * @return string $zip
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Sets the zip
     *
     * @param string $zip
     * @return void
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * Returns the city
     *
     * @return string $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the city
     *
     * @param string $city
     * @return void
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Returns the country
     *
     * @return string $country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Sets the country
     *
     * @param string $country
     * @return void
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Returns the domain model repository name
     *
     * @return string repository
     */
    public function getRepo()
    {
        return base64_encode($this->repo);
    }
}