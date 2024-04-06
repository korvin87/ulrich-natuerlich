<?php

namespace TYPO3\AbavoMaps\Domain\Model;

use Abavo\AbavoAddress\Domain\Model\Address;
use TYPO3\AbavoMaps\Domain\Repository\AbavoAddressRepository;
/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
/**
 * Ein Addresse als Marker einer Karte
 */
class AbavoAddress extends Address
{
    /**
     * Domain model repository name
     *
     * @var string
     */
    protected $repo = AbavoAddressRepository::class;

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