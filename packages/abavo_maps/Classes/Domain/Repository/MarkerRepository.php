<?php

namespace TYPO3\AbavoMaps\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
/*
 * abavo_maps
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
/**
 * The repository for Markers
 */
class MarkerRepository extends Repository
{
    /**
     * baseRepository
     *
     * @var BaseRepository
     */
    protected $baseRepository;

    /*
     * Find one db entry by ukey
     *
     * @return object
     */

    function findByUkey($ukey = 0)
    {
        return $this->baseRepository->findByUkey($this, $ukey);
    }

    public function injectBaseRepository(BaseRepository $baseRepository): void
    {
        $this->baseRepository = $baseRepository;
    }
}