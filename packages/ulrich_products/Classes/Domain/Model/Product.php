<?php
/**
 * Projekt (EXT:ulrich_products) - Product.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 23.05.2018 - 16:19:00
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 *
 */

namespace Abavo\UlrichProducts\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use SJBR\StaticInfoTables\Domain\Model\Country;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
/**
 * Produkt
 */
class Product extends AbstractEntity
{
    /**
     * Titel
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected $title = '';

    /**
     * Beschreibung
     *
     * @var string
     */
    protected $description = '';

    /**
     * accordiontextPlant
     *
     * @var string
     */
    protected $accordiontextPlant = '';

    /**
     * accordiontextOrigin
     *
     * @var string
     */
    protected $accordiontextOrigin = '';

    /**
     * accordiontextProduction
     *
     * @var string
     */
    protected $accordiontextProduction = '';

    /**
     * accordiontextApplication
     *
     * @var string
     */
    protected $accordiontextApplication = '';

    /**
     * accordiontextFacts
     *
     * @var string
     */
    protected $accordiontextFacts = '';

    /**
     * Aussehen
     *
     * @var string
     */
    protected $appearance = '';

    /**
     * CAS Nummer
     *
     * @var string
     */
    protected $casNumber = '';

    /**
     * EG Nummer
     *
     * @var string
     */
    protected $egNumber = '';

    /**
     * Granulierung
     *
     * @var string
     */
    protected $granulation = '';

    /**
     * Mindesthaltbarkeit
     *
     * @var string
     */
    protected $bestbefor = '';

    /**
     * Qualitäten
     *
     * @var string
     */
    protected $qualities = '';

    /**
     * Spezifikation
     *
     * @var string
     */
    protected $spec = '';

    /**
     * Aggregatzustand
     *
     * @var string
     */
    protected $physicalState = '';

    /**
     * Chemische Eigenschaften
     *
     * @var string
     */
    protected $chemicalProperties = '';

    /**
     * Molekulare Formel
     *
     * @var string
     */
    protected $molecularFormula = '';

    /**
     * Chemischer Name
     *
     * @var string
     */
    protected $chemicalName = '';

    /**
     * Registrierung
     *
     * @var string
     */
    protected $registration = '';

    /**
     * E-Nummer
     *
     * @var string
     */
    protected $eNumber = '';

    /**
     * Grass-Status
     *
     * @var string
     */
    protected $grassState = '';

    /**
     * Gebinde
     *
     * @var string
     */
    protected $container = '';

    /**
     * INCI
     *
     * @var string
     */
    protected $inci = '';

    /**
     * EINECS
     *
     * @var string
     */
    protected $einecs = '';

    /**
     * Schmelzpunkt
     *
     * @var string
     */
    protected $meltingPoint = '';

    /**
     * Haltbarkeit
     *
     * @var string
     */
    protected $durability = '';

    /**
     * Lagerung
     *
     * @var string
     */
    protected $storage = '';

    /**
     * NextDay
     *
     * @var boolean
     */
    protected $nextday = false;

    /**
     * Bilder
     *
     * @var ObjectStorage<FileReference>
     * @Extbase\ORM\Cascade("remove")
     */
    protected $media = null;

    /**
     * accordiontextPlantMedia
     *
     * @var ObjectStorage<FileReference>
     * @Extbase\ORM\Cascade("remove")
     */
    protected $accordiontextPlantMedia = null;

    /**
     * accordiontextOriginMedia
     *
     * @var ObjectStorage<FileReference>
     * @Extbase\ORM\Cascade("remove")
     */
    protected $accordiontextOriginMedia = null;

    /**
     * accordiontextProductionMedia
     *
     * @var ObjectStorage<FileReference>
     * @Extbase\ORM\Cascade("remove")
     */
    protected $accordiontextProductionMedia = null;

    /**
     * accordiontextApplicationMedia
     *
     * @var ObjectStorage<FileReference>
     * @Extbase\ORM\Cascade("remove")
     */
    protected $accordiontextApplicationMedia = null;

    /**
     * accordiontextFactsMedia
     *
     * @var ObjectStorage<FileReference>
     * @Extbase\ORM\Cascade("remove")
     */
    protected $accordiontextFactsMedia = null;

    /**
     * Herkunftsländer
     *
     * @var ObjectStorage<Country>
     * @Extbase\ORM\Lazy
     */
    protected $originCountries = null;

    /**
     * Verwandte Produkte
     *
     * @var ObjectStorage<\Abavo\UlrichProducts\Domain\Model\Product>
     * @Extbase\ORM\Lazy
     */
    protected $relatedProducts = null;

    /**
     * Ansprechpartner
     *
     * @var ObjectStorage<Contact>
     */
    protected $contacts = null;

    /**
     * Kategorien
     *
     * @var ObjectStorage<Category>
     * @Extbase\ORM\Lazy
     */
    protected $categories = null;

    /**
     * The detail view uri
     * @var string
     */
    protected $uri = null;

    /**
     * Branche
     *
     * @var int
     */
    protected $branch = null;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->media                            = new ObjectStorage();
        $this->accordiontextApplicationMedia    = new ObjectStorage();
        $this->accordiontextFactsMedia          = new ObjectStorage();
        $this->accordiontextOriginMedia         = new ObjectStorage();
        $this->accordiontextPlantMedia          = new ObjectStorage();
        $this->accordiontextProductionMedia     = new ObjectStorage();
        $this->relatedProducts                  = new ObjectStorage();
        $this->categories                       = new ObjectStorage();
        $this->originCountries                  = new ObjectStorage();
        $this->contacts                         = new ObjectStorage();
    }

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * Returns the accordiontextPlant
     *
     * @return string $accordiontextPlant
     */
    public function getAccordiontextPlant()
    {
        return $this->accordiontextPlant;
    }

    /**
     * Sets the accordiontextPlant
     *
     * @param string $accordiontextPlant
     * @return void
     */
    public function setAccordiontextPlant($accordiontextPlant)
    {
        $this->accordiontextPlant = $accordiontextPlant;
    }

    /**
     * Returns the accordiontextOrigin
     *
     * @return string $accordiontextOrigin
     */
    public function getAccordiontextOrigin()
    {
        return $this->accordiontextOrigin;
    }

    /**
     * Sets the accordiontextOrigin
     *
     * @param string $accordiontextOrigin
     * @return void
     */
    public function setAccordiontextOrigin($accordiontextOrigin)
    {
        $this->accordiontextOrigin = $accordiontextOrigin;
    }

    /**
     * Returns the accordiontextProduction
     *
     * @return string $accordiontextProduction
     */
    public function getAccordiontextProduction()
    {
        return $this->accordiontextProduction;
    }

    /**
     * Sets the accordiontextProduction
     *
     * @param string $accordiontextProduction
     * @return void
     */
    public function setAccordiontextProduction($accordiontextProduction)
    {
        $this->accordiontextProduction = $accordiontextProduction;
    }

    /**
     * Returns the accordiontextApplication
     *
     * @return string $accordiontextApplication
     */
    public function getAccordiontextApplication()
    {
        return $this->accordiontextApplication;
    }

    /**
     * Sets the accordiontextApplication
     *
     * @param string $accordiontextApplication
     * @return void
     */
    public function setAccordiontextApplication($accordiontextApplication)
    {
        $this->accordiontextApplication = $accordiontextApplication;
    }

    /**
     * Returns the accordiontextFacts
     *
     * @return string $accordiontextFacts
     */
    public function getAccordiontextFacts()
    {
        return $this->accordiontextFacts;
    }

    /**
     * Sets the accordiontextFacts
     *
     * @param string $accordiontextFacts
     * @return void
     */
    public function setAccordiontextFacts($accordiontextFacts)
    {
        $this->accordiontextFacts = $accordiontextFacts;
    }

    /**
     * Returns the appearance
     *
     * @return string $appearance
     */
    public function getAppearance()
    {
        return $this->appearance;
    }

    /**
     * Sets the appearance
     *
     * @param string $appearance
     * @return void
     */
    public function setAppearance($appearance)
    {
        $this->appearance = $appearance;
    }

    /**
     * Returns the casNumber
     *
     * @return string $casNumber
     */
    public function getCasNumber()
    {
        return $this->casNumber;
    }

    /**
     * Sets the casNumber
     *
     * @param string $casNumber
     * @return void
     */
    public function setCasNumber($casNumber)
    {
        $this->casNumber = $casNumber;
    }

    /**
     * Returns the egNumber
     *
     * @return string $egNumber
     */
    public function getEgNumber()
    {
        return $this->egNumber;
    }

    /**
     * Sets the egNumber
     *
     * @param string $egNumber
     * @return void
     */
    public function setEgNumber($egNumber)
    {
        $this->egNumber = $egNumber;
    }

    /**
     * Returns the granulation
     *
     * @return string $granulation
     */
    public function getGranulation()
    {
        return $this->granulation;
    }

    /**
     * Sets the granulation
     *
     * @param string $granulation
     * @return void
     */
    public function setGranulation($granulation)
    {
        $this->granulation = $granulation;
    }

    /**
     * Returns the bestbefor
     *
     * @return string $bestbefor
     */
    public function getBestbefor()
    {
        return $this->bestbefor;
    }

    /**
     * Sets the bestbefor
     *
     * @param string $bestbefor
     * @return void
     */
    public function setBestbefor($bestbefor)
    {
        $this->bestbefor = $bestbefor;
    }

    /**
     * Returns the qualities
     *
     * @return string $qualities
     */
    public function getQualities()
    {
        return $this->qualities;
    }

    /**
     * Returns the qualities for selection
     *
     * @return string $qualities
     */
    public function getQualitiesForSelection()
    {
        $qualities            = [];
        $commaseparatedValues = GeneralUtility::trimExplode(',', $this->qualities, true);
        if (is_array($commaseparatedValues)) {
            $qualities = array_merge($qualities, $commaseparatedValues);
        }
        $qualities[] = LocalizationUtility::translate('Form.quality.misc', 'UlrichProducts');

        return array_combine(array_values($qualities), array_values($qualities));
    }

    /**
     * Sets the qualities
     *
     * @param string $qualities
     * @return void
     */
    public function setQualities($qualities)
    {
        $this->qualities = $qualities;
    }

    /**
     * get the
     * 
     * @return type
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * get the
     * 
     * @return type
     */
    public function getPhysicalState()
    {
        return $this->physicalState;
    }

    /**
     * get the chemicalProperties
     * 
     * @return type
     */
    public function getChemicalProperties()
    {
        return $this->chemicalProperties;
    }

    /**
     * get the molecularFormula
     * 
     * @return type
     */
    public function getMolecularFormula()
    {
        return $this->molecularFormula;
    }

    /**
     * get the chemicalName
     * 
     * @return type
     */
    public function getChemicalName()
    {
        return $this->chemicalName;
    }

    /**
     * get the registration
     * 
     * @return type
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * get the eNumber
     * 
     * @return type
     */
    public function getENumber()
    {
        return $this->eNumber;
    }

    /**
     * get the grassState
     * 
     * @return type
     */
    public function getGrassState()
    {
        return $this->grassState;
    }

    /**
     * get the container
     * 
     * @return type
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * get the inci
     * 
     * @return type
     */
    public function getInci()
    {
        return $this->inci;
    }

    /**
     * get the einecs
     * 
     * @return type
     */
    public function getEinecs()
    {
        return $this->einecs;
    }

    /**
     * get the meltingPoint
     * 
     * @return type
     */
    public function getMeltingPoint()
    {
        return $this->meltingPoint;
    }

    /**
     * get the durability
     * 
     * @return type
     */
    public function getDurability()
    {
        return $this->durability;
    }

    /**
     * get the storage
     * 
     * @return type
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * set the 
     * 
     * @param string $spec
     * @return $this
     */
    public function setSpec($spec)
    {
        $this->spec = $spec;
        return $this;
    }

    /**
     * set the 
     * 
     * @param string $spec
     * @return $this
     */
    public function setPhysicalState($physicalState)
    {
        $this->physicalState = $physicalState;
        return $this;
    }

    /**
     * set the 
     * 
     * @param string $spec
     * @return $this
     */
    public function setChemicalProperties($chemicalProperties)
    {
        $this->chemicalProperties = $chemicalProperties;
        return $this;
    }

    /**
     * set the molecularFormula
     * 
     * @param string $spec
     * @return $this
     */
    public function setMolecularFormula($molecularFormula)
    {
        $this->molecularFormula = $molecularFormula;
        return $this;
    }

    /**
     * set the chemicalName
     * 
     * @param string $spec
     * @return $this
     */
    public function setChemicalName($chemicalName)
    {
        $this->chemicalName = $chemicalName;
        return $this;
    }

    /**
     * set the registration
     * 
     * @param string $spec
     * @return $this
     */
    public function setRegistration($registration)
    {
        $this->registration = $registration;
        return $this;
    }

    /**
     * set the eNumber
     * 
     * @param string $spec
     * @return $this
     */
    public function setENumber($eNumber)
    {
        $this->eNumber = $eNumber;
        return $this;
    }

    /**
     * set the grassState
     * 
     * @param string $spec
     * @return $this
     */
    public function setGrassState($grassState)
    {
        $this->grassState = $grassState;
        return $this;
    }

    /**
     * set the container
     * 
     * @param string $spec
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * set the inci
     * 
     * @param string $spec
     * @return $this
     */
    public function setInci($inci)
    {
        $this->inci = $inci;
        return $this;
    }

    /**
     * set the einecs
     * 
     * @param string $spec
     * @return $this
     */
    public function setEinecs($einecs)
    {
        $this->einecs = $einecs;
        return $this;
    }

    /**
     * set the meltingPoint
     * 
     * @param string $spec
     * @return $this
     */
    public function setMeltingPoint($meltingPoint)
    {
        $this->meltingPoint = $meltingPoint;
        return $this;
    }

    /**
     * set the durability
     * 
     * @param string $spec
     * @return $this
     */
    public function setDurability($durability)
    {
        $this->durability = $durability;
        return $this;
    }

    /**
     * set the storage
     * 
     * @param string $spec
     * @return $this
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
        return $this;
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

    /**
     * Adds a Product
     *
     * @param \Abavo\UlrichProducts\Domain\Model\Product $relatedProduct
     * @return void
     */
    public function addRelatedProduct(\Abavo\UlrichProducts\Domain\Model\Product $relatedProduct)
    {
        $this->relatedProducts->attach($relatedProduct);
    }

    /**
     * Removes a Product
     *
     * @param \Abavo\UlrichProducts\Domain\Model\Product $relatedProductToRemove The Product to be removed
     * @return void
     */
    public function removeRelatedProduct(\Abavo\UlrichProducts\Domain\Model\Product $relatedProductToRemove)
    {
        $this->relatedProducts->detach($relatedProductToRemove);
    }

    /**
     * Returns the relatedProducts
     *
     * @return ObjectStorage<\Abavo\UlrichProducts\Domain\Model\Product> $relatedProducts
     */
    public function getRelatedProducts()
    {
        return $this->relatedProducts;
    }

    /**
     * Sets the relatedProducts
     *
     * @param ObjectStorage<\Abavo\UlrichProducts\Domain\Model\Product> $relatedProducts
     * @return void
     */
    public function setRelatedProducts(ObjectStorage $relatedProducts)
    {
        $this->relatedProducts = $relatedProducts;
    }

    /**
     * Adds a Category
     *
     * @param Category $category
     * @return void
     */
    public function addCategory(Category $category)
    {
        $this->categories->attach($category);
    }

    /**
     * Removes a Category
     *
     * @param Category $categoryToRemove The Category to be removed
     * @return void
     */
    public function removeCategory(Category $categoryToRemove)
    {
        $this->categories->detach($categoryToRemove);
    }

    /**
     * Returns the categories
     *
     * @return ObjectStorage<Category> $categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Sets the categories
     *
     * @param ObjectStorage<Category> $categories
     * @return void
     */
    public function setCategories(ObjectStorage $categories)
    {
        $this->categories = $categories;
    }

    /**
     * get the nextday
     * 
     * @return boolean
     */
    public function getNextday()
    {
        return $this->nextday;
    }

    /**
     * set the nextday
     * 
     * @param boolean $nextday
     * @return $this
     */
    public function setNextday($nextday)
    {
        $this->nextday = $nextday;
        return $this;
    }

    /**
     * get the uri
     * 
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * set the uri
     * 
     * @param string $uri
     * @return $this
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * get the branch
     * 
     * @return array
     */
    public function getBranch()
    {
        if ($this->branch) {
            $pageRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(PageRepository::class);
            return $pageRepository->getPage($this->branch);
        }
    }

    /**
     * set the branch
     * 
     * @param int $branch
     * @return $this
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
        return $this;
    }

    /**
     * Adds a Country
     *
     * @param Country $country
     * @return void
     */
    public function addOriginCountries(Country $country)
    {
        $this->originCountries->attach($country);
    }

    /**
     * Removes a Country
     *
     * @param Country $countryToRemove The Country to be removed
     * @return void
     */
    public function removeOriginCountries(Country $countryToRemove)
    {
        $this->originCountries->detach($countryToRemove);
    }

    /**
     * Returns the originCountries
     *
     * @return ObjectStorage<Country> $originCountries
     */
    public function getOriginCountries()
    {
        return $this->originCountries;
    }

    /**
     * Sets the originCountries
     *
     * @param ObjectStorage<Country> $originCountries
     * @return void
     */
    public function setOriginCountries(ObjectStorage $originCountries)
    {
        $this->originCountries = $originCountries;
    }

    /**
     * Adds a Country
     *
     * @param Contact $contact
     * @return void
     */
    public function addContacts(Contact $contact)
    {
        $this->contacts->attach($contact);
    }

    /**
     * Removes a Country
     *
     * @param Contact $contactToRemove The Country to be removed
     * @return void
     */
    public function removeContacts(Contact $contactToRemove)
    {
        $this->contacts->detach($contactToRemove);
    }

    /**
     * Returns the contacts
     *
     * @return ObjectStorage<Contact> $contacts
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Sets the contacts
     *
     * @param ObjectStorage<Contact> $contacts
     * @return void
     */
    public function setContacts(ObjectStorage $contacts)
    {
        $this->contacts = $contacts;
    }

    /**
     * Returns the accordiontextPlantMedia
     *
     * @return ObjectStorage<FileReference> $accordiontextPlantMedia
     */
    public function getAccordiontextPlantMedia()
    {
        return $this->accordiontextPlantMedia;
    }

    /**
     * Sets the accordiontextPlantMedia
     *
     * @param ObjectStorage<FileReference> $accordiontextPlantMedia
     * @return void
     */
    public function setAccordiontextPlantMedia(ObjectStorage $accordiontextPlantMedia)
    {
        $this->accordiontextPlantMedia = $accordiontextPlantMedia;
    }

    /**
     * Returns the accordiontextOriginMedia
     *
     * @return ObjectStorage<FileReference> $accordiontextOriginMedia
     */
    public function getAccordiontextOriginMedia()
    {
        return $this->accordiontextOriginMedia;
    }

    /**
     * Sets the accordiontextOriginMedia
     *
     * @param ObjectStorage<FileReference> $accordiontextOriginMedia
     * @return void
     */
    public function setAccordiontextOriginMedia(ObjectStorage $accordiontextOriginMedia)
    {
        $this->accordiontextOriginMedia = $accordiontextOriginMedia;
    }

    /**
     * Returns the accordiontextProductionMedia
     *
     * @return ObjectStorage<FileReference> $accordiontextProductionMedia
     */
    public function getAccordiontextProductionMedia()
    {
        return $this->accordiontextProductionMedia;
    }

    /**
     * Sets the accordiontextProductionMedia
     *
     * @param ObjectStorage<FileReference> $accordiontextProductionMedia
     * @return void
     */
    public function setAccordiontextProductionMedia(ObjectStorage $accordiontextProductionMedia)
    {
        $this->accordiontextProductionMedia = $accordiontextProductionMedia;
    }

    /**
     * Returns the accordiontextApplicationMedia
     *
     * @return ObjectStorage<FileReference> $accordiontextApplicationMedia
     */
    public function getAccordiontextApplicationMedia()
    {
        return $this->accordiontextApplicationMedia;
    }

    /**
     * Sets the accordiontextApplicationMedia
     *
     * @param ObjectStorage<FileReference> $accordiontextApplicationMedia
     * @return void
     */
    public function setAccordiontextApplicationMedia(ObjectStorage $accordiontextApplicationMedia)
    {
        $this->accordiontextApplicationMedia = $accordiontextApplicationMedia;
    }

    /**
     * Returns the accordiontextFactsMedia
     *
     * @return ObjectStorage<FileReference> $accordiontextFactsMedia
     */
    public function getAccordiontextFactsMedia()
    {
        return $this->accordiontextFactsMedia;
    }

    /**
     * Sets the accordiontextFactsMedia
     *
     * @param ObjectStorage<FileReference> $accordiontextFactsMedia
     * @return void
     */
    public function setAccordiontextFactsMedia(ObjectStorage $accordiontextFactsMedia)
    {
        $this->accordiontextApplicationMedia = $accordiontextFactsMedia;
    }
}
