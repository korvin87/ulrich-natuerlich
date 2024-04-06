<?php
/*
 * abavo_form
 *
 * @copyright   2016 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use Abavo\AbavoForm\Domain\Repository\FormRepository;
use SJBR\StaticInfoTables\Domain\Model\Country;
use SJBR\StaticInfoTables\Domain\Model\CountryZone;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use SJBR\StaticInfoTables\Domain\Repository\CountryRepository;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Crypto\Random;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Form
 */
class Form extends AbstractEntity
{
    public const SESSION_KEY                   = 'Form';
    public const DEFAULT_COUNTRY               = 'DEU'; //by IsoCodeA3
    public const STEPS_COUNT                   = 1; // Multistep form
    public const ALLOW_EXT_PROPERTIES_EXPLICIT = false; // allow extended properties outside rendered of f:form viewhelpers
    public const RESET_AFTER_CREATE            = true;

    /**
     * RepositoryClass
     *
     * Change this if you extended this model and this is mapped to an other table as default to able the unique_id calculation
     *
     * @var string
     */
    protected $repositoryClass = FormRepository::class;

    /**
     * uniqueId
     *
     * @var string
     * @Extbase\Validate("\Abavo\AbavoForm\Validation\Validator\UniqueIdValidator")
     */
    protected $uniqueId = null;

    /**
     * stepper
     *
     * @var Stepper
     * @ignorevalidation
     */
    protected $stepper = null;

    /**
     * salutation
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("Text")
     * @Extbase\Validate("StringLength", options={"minimum": 2, "maximum": 25})
     */
    protected $salutation = '';

    /**
     * Firstname
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("Text")
     * @Extbase\Validate("StringLength", options={"minimum": 3, "maximum": 50})
     */
    protected $firstname = '';

    /**
     * Lastname
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("Text")
     * @Extbase\Validate("StringLength", options={"minimum": 3, "maximum": 50})
     */
    protected $lastname = '';

    /**
     * Phone
     *
     * @var string
     */
    protected $phone = '';

    /**
     * E-Mail
     *
     * @var string
     * @Extbase\Validate("EmailAddress")
     * @Extbase\Validate("NotEmpty")
     */
    protected $email = '';

    /**
     * company
     *
     * @var string
     * @Extbase\Validate("Text")
     * @Extbase\Validate("StringLength", options={"minimum": 0, "maximum": 50})
     */
    protected $company = '';

    /**
     * address
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("Text")
     * @Extbase\Validate("StringLength", options={"minimum": 3, "maximum": 50})
     */
    protected $address = '';

    /**
     * zip
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("Text")
     * @Extbase\Validate("StringLength", options={"minimum": 3, "maximum": 50})
     */
    protected $zip = '';

    /**
     * city
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("Text")
     * @Extbase\Validate("StringLength", options={"minimum": 3, "maximum": 50})
     */
    protected $city = '';

    /**
     * country
     *
     * @var Country
     * @Extbase\ORM\Lazy
     * @Extbase\Validate("NotEmpty")
     */
    protected $country = null;

    /**
     * countryZone
     *
     * @var CountryZone
     * @Extbase\ORM\Lazy
     */
    protected $countryZone = '';

    /**
     * description
     *
     * @var string
     * @Extbase\Validate("Text")
     * @Extbase\Validate("StringLength", options={"minimum": 0, "maximum": 2000})
     */
    protected $description = '';

    /**
     * datetime
     *
     * @var string
     */
    protected $datetime = null;

    /**
     * processed
     *
     * @var string
     */
    protected $processed = false;

    /**
     * media
     *
     * @var array
     * @Extbase\Validate("\Abavo\AbavoForm\Validation\Validator\UploadValidator", options={"maxfilesize": PHP_INI, "filetypes": pdf|jpg|jpeg|png})
     */
    protected $media = null;

    /**
     * privacyhint
     *
     * @var boolean
     * @Extbase\Validate("\Abavo\AbavoForm\Validation\Validator\GenericBooleanValidator", options={"is": true, "notTrueMessage": AbavoForm:Form.error.privacyhint})
     */
    protected $privacyhint = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Init uniqueId
        if ((boolean) $this->getUniqueId() === false) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $repository    = $objectManager->get($this->repositoryClass);
            $querySettings = $repository->createQuery()->getQuerySettings()->setRespectStoragePage(false);
            $repository->setDefaultQuerySettings($querySettings);

            $this->uniqueId = $this->getNewUniqueIdByRepository($repository);
        }

        // Set DateTime
        $this->datetime = new \DateTime;

        // Set Stepper
        $this->stepper = Stepper::getInstance($this::STEPS_COUNT);

        // Set default country object
        if (!$this->country instanceof Country) {
            $objectManager     = GeneralUtility::makeInstance(ObjectManager::class);
            $countryRepository = $objectManager->get(CountryRepository::class);

            if (($country = $countryRepository->findOneByIsoCodeA3($this::DEFAULT_COUNTRY)) instanceof Country) {
                $this->country = $country;
            }
        }
    }

    /**
     * Get new uniqueId method
     *
     * @return string
     */
    public function getNewUniqueIdByRepository(Repository $repository)
    {
        $newUniqueId = $this->generateUniqueIdentifier();
        return ($repository->findOneByUniqueId($newUniqueId) instanceof Inquire) ? $this->getNewUniqueIdByRepository($repository) : $newUniqueId;
    }

    /**
     * Generate a unique identifier
     *
     * @return string
     */
    private function generateUniqueIdentifier()
    {
        $randomString = $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'].GeneralUtility::makeInstance(Random::class)->generateRandomHexString(10).time();
        return PasswordHashFactory::getSaltingInstance(NULL)->getHashedPassword($randomString);
    }

    /**
     * Sets the uniqueId
     *
     * @param string $uniqueId
     * @return void
     */
    public function setUniqueId($uniqueId)
    {
        $this->uniqueId = $uniqueId;
    }

    /**
     * Returns the uniqueId
     *
     * @return string $uniqueId
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * Get the repository class
     * 
     * @return string
     */
    public function getRepositoryClass()
    {
        return $this->repositoryClass;
    }

    /**
     * Sets the stepper
     *
     * @param Stepper $stepper
     * @return void
     */
    public function setStepper($stepper)
    {
        $this->stepper = $stepper;
    }

    /**
     * Returns the stepper
     *
     * @return string $stepper
     */
    public function getStepper()
    {
        return $this->stepper;
    }

    /**
     * Returns the salutation
     *
     * @return string $salutation
     */
    public function getSalutation()
    {
        return $this->salutation;
    }

    /**
     * Returns the possible salutations
     *
     * @return array $salutations
     */
    public function getSalutations()
    {
        return [
            'Form.salutations.mr' => LocalizationUtility::translate('Form.salutations.mr', 'abavo_form'),
            'Form.salutations.ms' => LocalizationUtility::translate('Form.salutations.ms', 'abavo_form')
        ];
    }

    /**
     * Sets the salutation
     *
     * @param string $salutation
     * @return void
     */
    public function setSalutation($salutation)
    {
        $this->salutation = $salutation;
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
        $this->firstname = $firstname;
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
     * Returns the company
     *
     * @return string $company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Sets the company
     *
     * @param string $company
     * @return void
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * Returns the address
     *
     * @return string $address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Sets the address
     *
     * @param string $address
     * @return void
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
     * @return Country $country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Sets the country
     *
     * @param Country $country
     * @return void
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Returns the countryZone
     *
     * @return CountryZone $countryZone
     */
    public function getCountryZone()
    {
        return $this->countryZone;
    }

    /**
     * Sets the countryZone
     *
     * @param CountryZone $countryZone
     * @return void
     */
    public function setCountryZone($countryZone)
    {
        $this->countryZone = $countryZone;
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
     * Returns the datetime
     *
     * @return \DateTime $datetime
     */
    public function getDatetime()
    {
        return($this->datetime instanceof \DateTime) ? $this->datetime : \DateTime::createFromFormat('Y-m-d H:i:s', $this->datetime, $this->getDatetimezone());
    }

    /**
     * Sets the Datetime
     *
     * @param \DateTime $datetime
     * @return void
     */
    public function setDatetime($datetime)
    {
        $this->datetime = ($datetime instanceof \DateTime) ? $datetime->format('Y-m-d H:i:s') : $datetime;
    }

    /**
     * Sets the processed
     *
     * @param boolean $processed
     * @return void
     */
    public function setProcessed($processed)
    {
        $this->processed = (boolean) $processed;
    }

    /**
     * Returns the processed
     *
     * @return boolean $processed
     */
    public function isProcessed()
    {
        return $this->processed;
    }

    /**
     * Returns the media
     *
     * @return array
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Sets the media
     *
     * @param array
     * @return void
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }

    /**
     * Returns the reset constant
     *
     * @return boolean
     */
    public function isResetAfterCreate()
    {
        return $this::RESET_AFTER_CREATE;
    }

    /**
     * Set the uid
     * 
     * @param int $uid
     */
    public function setUid(int $uid)
    {
        $this->uid = (int) $uid;
    }

    /**
     * Returns the privacyhint
     *
     * @return boolean $privacyhint
     */
    public function getPrivacyhint()
    {
        return $this->privacyhint;
    }

    /**
     * Returns the privacyhint
     *
     * @return boolean $privacyhint
     */
    public function isPrivacyhint()
    {
        return $this->privacyhint;
    }

    /**
     * Sets the privacyhint
     *
     * @param boolean $privacyhint
     * @return void
     */
    public function setPrivacyhint(bool $privacyhint)
    {
        $this->privacyhint = $privacyhint;
    }
}