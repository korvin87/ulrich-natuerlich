<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
/**
 * DomainModel Term
 *
 * @author mbruckmoser
 */
class Term extends AbstractEntity
{
    /**
     * refid
     *
     * @var string
     */
    protected $refid = '';

    /**
     * search
     *
     * @var string
     */
    protected $search = '';

    /**
     * fegroup
     *
     * @var string
     */
    protected $fegroup = '0';

    /**
     * tstamp
     *
     * @var int
     */
    protected $tstamp = 0;

    /**
     * crdate
     *
     * @var int
     */
    protected $crdate = 0;

    /**
     * sysLanguageUid
     *
     * @var int
     */
    protected $sysLanguageUid = 0;

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
     * @return void
     */
    public function setRefid($refid)
    {
        $this->refid = $refid;
    }

    /**
     * Returns the search
     *
     * @return string $search
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Sets the search
     *
     * @param string $search
     * @return void
     */
    public function setSearch($search)
    {
        $this->search = $search;
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
     * Sets the fegroup
     *
     * @param string $fegroup
     * @return void
     */
    public function setFegroup($fegroup)
    {
        $this->fegroup = $fegroup;
    }

    /**
     * Returns the tstamp
     *
     * @return int $tstamp
     */
    public function getTstamp()
    {
        return $this->tstamp;
    }

    /**
     * Sets the tstamp
     *
     * @param int $tstamp
     * @return void
     */
    public function setTstamp($tstamp)
    {
        $this->tstamp = $tstamp;
    }

    /**
     * Returns the crdate
     *
     * @return int $crdate
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * Sets the crdate
     *
     * @param int $crdate
     * @return void
     */
    public function setCrdate($crdate)
    {
        $this->crdate = $crdate;
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
     * @return void
     */
    public function setSysLanguageUid($sysLanguageUid)
    {
        $this->sysLanguageUid = $sysLanguageUid;
    }
}