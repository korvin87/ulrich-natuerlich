<?php
/**
 * ulrich_products - ApiQuery.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 29.05.2018 - 10:31:28
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\Domain\Model\Product;

use TYPO3\CMS\Extbase\DomainObject\AbstractValueObject;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Abavo\UlrichProducts\Domain\Model\Category;

/**
 * ApiQuery
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class ApiQuery extends AbstractValueObject implements \JsonSerializable
{
    /**
     * the filter character
     *
     * @var string
     */
    protected $char = null;

    /**
     * che category
     *
     * @var Category
     */
    protected $category = null;

    /**
     * the branch
     *
     * @var int
     */
    protected $branch = 0;

    /**
     * the limit
     *
     * @var int
     */
    protected $limit = 10;

    /**
     * the offset
     *
     * @var int
     */
    protected $offset = 0;

    /**
     * the product properties
     *
     * @var string
     */
    protected $productProperties = 'uid, title, description, categories, media, contact, uri, originCountry';

    /**
     * Get a instance of this class
     * 
     * @param Category $category
     * @param int $branch
     * @return \Abavo\UlrichProducts\Domain\Model\Product\ApiQuery
     */
    public static function getInstance(Category $category = null, $branch = null)
    {
        $instance = GeneralUtility::makeInstance(static::class);

        if ($category) {
            $instance->setCategory($category);
        }

        if ($branch) {
            $instance->setBranch($branch);
        }

        return $instance;
    }

    /**
     * json serialize method
     * 
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->__toArray();
    }

    /**
     * to array convert method
     * 
     * @return array
     */
    public function __toArray()
    {
        return [
            'char' => $this->getChar(),
            'category' => ($this->getCategory() instanceof Category) ? $this->getCategory()->_getProperty('_localizedUid') : 0,
            'branch' => $this->getBranch(),
            'limit' => $this->getLimit(),
            'offset' => $this->getOffset(),
            'productProperties' => $this->getProductProperties()
        ];
    }

    /**
     * to string convert method
     */
    public function __toString()
    {
        \json_encode($this->jsonSerialize(), JSON_FORCE_OBJECT);
    }

    /**
     * get the char
     * 
     * @return string
     */
    public function getChar()
    {
        return $this->char;
    }

    /**
     * get the category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * get the limit
     * 
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * get the offset
     * 
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * set the char
     * 
     * @param string $char
     * @return $this
     */
    public function setChar($char)
    {
        $this->char = (trim($char)) ? preg_replace('/[^A-Z\']/', '', $char) : null;
        return $this;
    }

    /**
     * set the category
     *
     * @param Category $category
     * @return $this
     */
    public function setCategory($category)
    {
        if ($category instanceof Category) {
            $this->category = $category;
        }
        return $this;
    }

    /**
     * get the branch
     * 
     * @return int
     */
    public function getBranch()
    {
        return $this->branch;
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
     * set the limit
     * 
     * @param int $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * set the offset
     * 
     * @param int $offset
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * get the productProperties
     * 
     * @return string
     */
    public function getProductProperties()
    {
        return $this->productProperties;
    }

    /**
     * set the productProperties
     * 
     * @param string $productProperties
     * @return $this
     */
    public function setProductProperties($productProperties)
    {
        $this->productProperties = $productProperties;
        return $this;
    }
}