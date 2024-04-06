<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractValueObject;
/**
 * DomainModel Synonym
 *
 * @author mbruckmoser
 */
class Synonym extends AbstractValueObject
{
    /**
     * Titel
     *
     * @var \string
     * @Extbase\Validate("NotEmpty")
     */
    protected $title;

    /**
     * alt
     *
     * @var \string
     * @Extbase\Validate("NotEmpty")
     */
    protected $alt;

    /**
     * Synonym
     *
     * @var \string
     * @Extbase\Validate("NotEmpty")
     */
    protected $synonym;

    /**
     * Returns the title
     *
     * @return \string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param \string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the alt
     *
     * @return \string $alt
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Sets the alt
     *
     * @param \string $alt
     * @return void
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
    }

    /**
     * Returns the synonym
     *
     * @return \string $synonym
     */
    public function getSynonym()
    {
        return $this->synonym;
    }

    /**
     * Sets the synonym
     *
     * @param \string $synonym
     * @return void
     */
    public function setSynonym($synonym)
    {
        $this->synonym = $synonym;
    }
}
?>