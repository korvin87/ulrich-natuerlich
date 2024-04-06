<?php
/**
 * ulrich_products - Category.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 30.05.2018 - 10:55:00
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\Domain\Model;

/**
 * Category
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class Category extends \TYPO3\CMS\Extbase\Domain\Model\Category
{

    /**
     * Override \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject::__toString method
     * 
     * because this is wrong in case of multilanguage: return get_class($this) . ':' . (string)$this->uid; 
     * Returns the class name and the uid of the object as string
     * 
     * @return string
     */
    public function __toString()
    {
        return get_class($this).':'.(string) $this->_localizedUid;
    }

    /**
     * get the _localizedUid
     * 
     * @return int
     */
    public function getLocalizedUid()
    {
        return $this->_localizedUid;
    }
}