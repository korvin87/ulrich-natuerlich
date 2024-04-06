<?php
/**
 * ulrich_products - Inquiry.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 30.05.2018 - 15:25:43
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\Domain\Model\Product;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use Abavo\AbavoForm\Domain\Model\Form;
use SJBR\StaticInfoTables\Domain\Model\Country;
use Abavo\UlrichProducts\Domain\Model\Product;
/**
 * Inquiry
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class Inquiry extends Form
{
    /**
     * salutation
     *
     * @var string
     */
    protected $salutation = '';

    /**
     * Firstname
     *
     * @var string
     */
    protected $firstname = '';

    /**
     * Lastname
     *
     * @var string
     */
    protected $lastname = '';

    /**
     * Phone
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("Text")
     * @Extbase\Validate("StringLength", options={"minimum": 3, "maximum": 50})
     */
    protected $phone = '';

    /**
     * company
     *
     * @var string
     *
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("Text")
     * @Extbase\Validate("StringLength", options={"minimum": 3, "maximum": 50})
     */
    protected $company = '';

    /**
     * address
     *
     * @var string
     */
    protected $address = '';

    /**
     * zip
     *
     * @var string
     */
    protected $zip = '';

    /**
     * city
     *
     * @var string
     */
    protected $city = '';

    /**
     * country
     *
     * @var Country
     * @Extbase\ORM\Lazy
     */
    protected $country = null;

    /**
     * name
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("Text")
     * @Extbase\Validate("StringLength", options={"minimum": 3, "maximum": 50})
     */
    protected $name = '';

    /**
     * The product
     *
     * @var Product
     */
    protected $product = null;

    /**
     * quality
     *
     * @var string
     * @Extbase\Validate("Text")
     */
    protected $quality = null;

    /**
     * the standing
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("Text")
     * @Extbase\Validate("StringLength", options={"minimum": 3, "maximum": 50})
     */
    protected $standing = '';

    /**
     * get the name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * set the name
     * 
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * get the product
     *
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * set the product
     *
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * get the standing
     * 
     * @return string
     */
    public function getStanding()
    {
        return $this->standing;
    }

    /**
     * set the standing
     * 
     * @param string $standing
     * @return $this
     */
    public function setStanding($standing)
    {
        $this->standing = $standing;
        return $this;
    }

    /**
     * get the quality
     * 
     * @return string
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * set the quality
     * 
     * @param string $quality
     * @return $this
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;
        return $this;
    }
}