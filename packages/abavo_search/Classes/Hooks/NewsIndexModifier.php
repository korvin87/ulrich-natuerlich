<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Hooks;

use GeorgRinger\News\Domain\Model\News;
use Abavo\AbavoSearch\Domain\Exception\IndexException;

/**
 * NewsIndexModifier
 *
 * @author mbruckmoser
 */
class NewsIndexModifier
{
    public const CATEGORIES_CLUE = ' / ';

    function modifyIndex(&$recordObj, &$title, &$abstract, &$content)
    {

        try {
            $categories = [];
            if ($recordObj instanceof News) {
                foreach ($recordObj->getCategories() as $category) {
                    $categories[] = $category->getTitle();
                }

                if (!empty($categories)) {
                    $title = implode(self::CATEGORIES_CLUE, $categories).': '.$title;
                }
            }
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }
}