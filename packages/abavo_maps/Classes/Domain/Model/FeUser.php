<?php

namespace TYPO3\AbavoMaps\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\AbavoMaps\Domain\Repository\FeUserRepository;
/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
/**
 * A Frontend User
 *
 */
class FeUser extends FrontendUser
{
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
     * Domain model repository name
     *
     * @var string
     */
    protected $repo = FeUserRepository::class;

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
     * Returns the domain model repository name
     *
     * @return string repository
     */
    public function getRepo()
    {
        return base64_encode($this->repo);
    }
}