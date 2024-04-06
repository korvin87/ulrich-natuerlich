<?php
/**
 * ulrich_products - Filter.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 29.05.2018 - 11:14:40
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\Domain\Model\Product;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Filter
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class Filter
{
    public const ALL_KEY = 0;

    /**
     * the menu
     *
     * @var array
     */
    protected $menu = [];

    /**
     * Get a instance of this class
     * 
     * @return \Abavo\UlrichProducts\Domain\Model\Product\Filter
     */
    public static function getInstance()
    {
        $instance = new Filter;
        $instance->generateMenu();
        return $instance;
    }

    /**
     * Generate and set the menu method
     */
    private function generateMenu()
    {
        // create A to Z range
        $this->createNewMenuItem(self::ALL_KEY, LocalizationUtility::translate('label.aToZ.filter.all', 'UlrichProducts'), true);
        foreach (range('A', 'Z') as $char) {
            $this->createNewMenuItem($char, $char);
        }
    }

    /**
     * create a new menu item
     * 
     * @param string $char
     * @param string $label
     * @param bool $active
     * @param int $count
     * @return \stdClass
     */
    private function createNewMenuItem($char = null, $label = null, $active = false, $count = 0)
    {
        $this->menu[$char] = (object) [
                'char' => $char,
                'label' => $label,
                'count' => $count,
                'active' => $active
        ];
    }

    /**
     * update the menu by a character
     * 
     * @param string $char
     * @throws \Exception
     */
    public function updateMenu($char = null)
    {
        if (isset($this->menu[$char])) {
            $this->menu[$char]->count++;
            $this->menu[self::ALL_KEY]->count++;
        } else {
            throw new \Exception('Impossible to sort character "'.$char.'"');
        }

        ksort($this->menu, SORT_ASC);
    }

    /**
     * get the menu
     * 
     * @return \stdClass
     */
    public function getMenu()
    {
        return $this->menu;
    }
}