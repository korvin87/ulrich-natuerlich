<?php
/**
 * Projekt (EXT:ulrich_products) - Contact.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 23.05.2018 - 16:19:00
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
/**
 * Ansprechpartner
 */
class Contact extends AbstractEntity
{
    /**
     * Name
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected $name = '';

    /**
     * Position
     *
     * @var string
     */
    protected $position = '';

    /**
     * Telefonnummer
     *
     * @var string
     */
    protected $phone = '';

    /**
     * E-Mail-Addresse
     *
     * @var string
     */
    protected $email = '';

    /**
     * Bilder
     *
     * @var ObjectStorage<FileReference>
     * @Extbase\ORM\Cascade("remove")
     */
    protected $media = null;

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the position
     *
     * @return string $position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the position
     *
     * @param string $position
     * @return void
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Returns the phone
     *
     * @return string $phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Sets the phone
     *
     * @param string $phone
     * @return void
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Returns the email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email
     *
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Adds a FileReference
     *
     * @param FileReference $medium
     * @return void
     */
    public function addMedium(FileReference $medium)
    {
        $this->media->attach($medium);
    }

    /**
     * Removes a FileReference
     *
     * @param FileReference $mediumToRemove The FileReference to be removed
     * @return void
     */
    public function removeMedium(FileReference $mediumToRemove)
    {
        $this->media->detach($mediumToRemove);
    }

    /**
     * Returns the media
     *
     * @return ObjectStorage<FileReference> $media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Sets the media
     *
     * @param ObjectStorage<FileReference> $media
     * @return void
     */
    public function setMedia(ObjectStorage $media)
    {
        $this->media = $media;
    }
}
