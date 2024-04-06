<?php
/**
 * abavo_search - AutocompleteItem.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 06.06.2018 - 08:00:06
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractValueObject;
/**
 * AutocompleteItem
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class AutocompleteItem extends AbstractValueObject implements \JsonSerializable
{
    /**
     * the label
     *
     * @var string
     */
    protected $label = '';

    /**
     * the term for search uri
     *
     * @var string
     */
    protected $term = '';

    /**
     * the term for search uri
     *
     * @var string
     */
    protected $uri = '';

    /**
     * the advanced data
     * @var array
     */
    protected $data = [];

    /**
     * the constructor
     * 
     * @param string $label
     * @param string $term
     * @param array $data
     */
    public function __construct(string $label = null, string $term = null, array $data = null, string $uri = null)
    {
        $this->setLabel($label ?? $this->label)
            ->setTerm($term ?? $label ?? $this->term)
            ->setData($data ?? $this->data)
            ->setUri($uri ?? $this->uri);
    }

    /**
     * get the label
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * get the term
     * 
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * get the data
     * 
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * set the label
     * 
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * set the term
     * 
     * @param string $term
     * @return $this
     */
    public function setTerm($term)
    {
        $this->term = preg_replace('!\s+!', ' ', preg_replace('/[^A-Za-z0-9ÄÖÜäöüß\-]/', ' ', $term));
        return $this;
    }

    /**
     * set the data
     * 
     * @param array $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * get the uri
     * 
     * @return array
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
        $properties = get_object_vars($this);
        foreach ($properties as $propertyName => $propertyValue) {
            if ($propertyName[0] === '_' || $propertyName === 'uid' || $propertyName === 'pid') {
                unset($properties[$propertyName]);
            }
        }
        return $properties;
    }

    /**
     * to string convert method
     */
    public function __toString()
    {
        \json_encode($this->jsonSerialize(), JSON_FORCE_OBJECT);
    }
}