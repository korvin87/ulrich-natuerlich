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
 * DomainModel Stat
 *
 * @author mbruckmoser
 */
class Stat extends AbstractEntity
{
    /**
     * refid
     *
     * @var string
     */
    protected $refid = null;

    /**
     * type
     *
     * @var string
     */
    protected $type = null;

    /**
     * val
     *
     * @var string
     */
    protected $val = '';

    /**
     * tstamp
     *
     * @var int
     */
    protected $tstamp = 0;

    /**
     * hits
     *
     * @var int
     */
    protected $hits = 0;

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
     * Returns the type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns the val
     *
     * @return string $val
     */
    public function getVal()
    {
        return $this->val;
    }

    /**
     * Sets the val
     *
     * @param string $val
     * @return void
     */
    public function setVal($val)
    {
        $this->val = $val;
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
     * @return void
     */
    public function setHits($hits)
    {
        $this->hits = $hits;
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