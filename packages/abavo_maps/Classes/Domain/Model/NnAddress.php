<?php

namespace TYPO3\AbavoMaps\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\AbavoMaps\Domain\Repository\NnAddressRepository;
/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
class NnAddress extends AbstractEntity
{
    /**
     * Name
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected $name = '';

    /**
     * Person
     *
     * @var ObjectStorage<\NN\NnAddress\Domain\Model\Person>
     * @Extbase\ORM\Lazy
     */
    protected $person;

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
     * Street
     *
     * @var string
     */
    protected $street = '';

    /**
     * Vollständige Adresse
     *
     * @var string
     */
    protected $streetnr = '';

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
     * Marker Link (optional: If set, don´t show description on clicking marker and
     * refer to link target)
     *
     * @var string
     */
    protected $url = '';

    /**
     * Beschreibung
     *
     * @var string
     */
    protected $description = '';

    /**
     * Domain model repository name
     *
     * @var string
     */
    protected $repo = NnAddressRepository::class;

    /**
     * Returns the name
     *
     * @return string $title
     */
    public function getName()
    {
        $name    = '';
        $persons = $this->person;
        foreach ($persons as $person) {
            $name .= $person->getFirstName().' '.$person->getLastName().' '.$person->getOrganisation();
        }

        return $name;
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
        return $this->street.' '.$this->streetnr.' '.$this->zip.' '.$this->city.' '.$this->country;
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

    /**
     * Returns the street
     *
     * @return string $street
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Sets the street
     *
     * @param string $street
     * @return void
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * Returns the streetnr
     *
     * @return string $streetnr
     */
    public function getStreetnr()
    {
        return $this->streetnr;
    }

    /**
     * Sets the streetnr
     *
     * @param string $streetnr
     * @return void
     */
    public function setStreetnr($streetnr)
    {
        $this->streetnr = $streetnr;
    }

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription()
    {

        $xml    = simplexml_load_string($this->description);
        $json   = json_encode($xml, JSON_THROW_ON_ERROR);
        $arrXml = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        return $arrXml['data']['sheet']['language']['field']['value'] ?: null;
    }
}