<?php
/**
 * abavo_search - ModifyItemExample.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 08.06.2018 - 11:03:25
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Api;

use GeorgRinger\News\Domain\Model\News;
use \TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * ModifyItemExample
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class ModifyItemExample implements ModifyItemInterface
{

    /**
     * Modify a item method
     * 
     * @param mixed $items
     */
    public function modfiyItem(&$item = null)
    {
        if ($item instanceof News) {
            $title = $item->getTitle();

            if ($item->getCategories()) {
                foreach ($item->getCategories() as $category) {
                    $categories[] = $category->getTitle();
                }

                if (!empty($categories)) {
                    $title = implode(' / ', $categories).': '.$title;
                }
            }

            $item->setTitle($title.' [index item modified by '.__METHOD__.']');

            // NOTE: "importId" is the workaround; use localizedUid in your project if possible!
            ObjectAccess::setProperty($item, 'importId', ObjectAccess::getProperty($item, '_localizedUid'));
        }
    }
}