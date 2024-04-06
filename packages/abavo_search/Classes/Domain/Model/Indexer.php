<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
/**
 * DomainModel Indexer Configuration
 *
 * @author mbruckmoser
 */
class Indexer extends AbstractEntity
{
    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * storagepid
     *
     * @var integer
     */
    protected $storagepid = 0;

    /**
     * target
     *
     * @var string
     */
    protected $target = '';

    /**
     * categories
     *
     * @var string
     */
    protected $categories = '';

    /**
     * type
     *
     * @var string
     */
    protected $type = '';

    /**
     * config
     *
     * @var string
     */
    protected $config = '';

    /**
     * locale
     *
     * @var string
     */
    protected $locale = '';

    /**
     * priority
     *
     * @var int
     */
    protected $priority = 0;

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
     * Returns the storagepid
     *
     * @return integer $storagepid
     */
    public function getStoragepid()
    {
        return $this->storagepid;
    }

    /**
     * Sets the storagepid
     *
     * @param integer $storagepid
     * @return void
     */
    public function setStoragepid($storagepid)
    {
        $this->storagepid = $storagepid;
    }

    /**
     * Returns the target
     *
     * @return string $target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Sets the target
     *
     * @param string $target
     * @return void
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * Returns the categories
     *
     * @return string $categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Sets the categories
     *
     * @param string $categories
     * @return void
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * Returns the type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Returns the config
     *
     * @return string $config
     */
    public function getConfig()
    {
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        return $flexFormService->convertFlexFormContentToArray($this->config);
    }

    /**
     * Sets the config
     *
     * @param string $config
     * @return void
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * Returns the locale
     *
     * @return string $locale
     */
    public function getLocale()
    {
        $return = '';
        if ($this->locale != '') {

            $arrLocale = [];
            foreach (GeneralUtility::trimExplode("\n", $this->locale, true) as $locale) {
                $tempLocale = GeneralUtility::trimExplode('=', $locale, true);
                if (!empty($tempLocale) && count($tempLocale) === 2) {
                    $mappedLocale = array_combine(['lang', 'translation'], $tempLocale);
                    if ($mappedLocale['lang'] == $GLOBALS['TSFE']->lang) {
                        $return = trim($mappedLocale['translation']);
                    }
                }
            }
        }

        return ($return == '') ? $this->title : $return;
    }

    /**
     * Sets the locale
     *
     * @param string $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this->config = $locale;
    }

    /**
     * Returns the priority
     *
     * @return int $priority
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Sets the priority
     *
     * @param int $priority
     * @return void
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }
}