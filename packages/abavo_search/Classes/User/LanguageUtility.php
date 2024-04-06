<?php
/**
 * abavo_search - LanguageUtility.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 04.06.2018 - 13:28:22
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\AbavoSearch\User;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility as T3LocalizationUtility;
use SJBR\StaticInfoTables\Domain\Repository\LanguageRepository;
use SJBR\StaticInfoTables\Domain\Repository\SystemLanguageRepository;
use Abavo\AbavoSearch\User\ConfigHelper;

/**
 * LanguageUtility
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class LanguageUtility implements SingletonInterface
{
    /**
     * the extension name based the translation
     *
     * @var string
     */
    public $extensionName = '';

    /**
     * the locallang path
     *
     * @var string
     */
    public $locallangPath = 'Resources/Private/Language/';

    /**
     * @var LocalizationFactory 
     */
    protected $localizationFactory = null; #

    /**
     * @var ObjectManager 
     */
    protected $objectManager = null;

    /**
     * @var array
     */
    protected $tsSetup = [];

    /**
     * The constructor
     */
    public function __construct()
    {
        $this->objectManager       = GeneralUtility::makeInstance(ObjectManager::class);
        $this->localizationFactory = GeneralUtility::makeInstance(LocalizationFactory::class);
        $this->tsSetup             = ConfigHelper::getInstance()->setup;
        $this->extensionName       = GeneralUtility::camelCaseToLowerCaseUnderscored(GeneralUtility::trimExplode('\\', __NAMESPACE__)[1]);
    }

    /**
     * Get a instance of this class
     * 
     * @return static::class
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(static::class);
    }

    /**
     * Translate method (Fallback to TYPO3 core LocalizationUtility if nothing found)
     * 
     * This method could be used in all contexts (FE/BE/CLI) :-)
     * 
     * @param string $key language label key
     * @param string $language the isoA2code for target language
     * @param string $extensionName the extension name
     * @return string
     */
    public function translate(string $key, $language = 'default', $extensionName = null)
    {
        $localizationFile = null;
        $extensionName ??= $this->extensionName;

        if (\str_starts_with($key, 'EXT:')) {
            $keyArray = GeneralUtility::trimExplode(':', $key, true);
            if (isset($keyArray[0]) && isset($keyArray[1])) {
                $localizationFile = $keyArray[0].':'.$keyArray[1];
                $key              = str_replace($localizationFile.':', '', $key);
            }
        } else {
            $localizationFile = 'EXT:'.GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName).'/'.$this->locallangPath.'locallang.xlf';
        }
        $localizationArr = $this->localizationFactory->getParsedData($localizationFile, $language, 'utf-8');

        return $localizationArr[$language][$key][0]['target'] ?? $localizationArr['default'][$key][0]['target'] ?? T3LocalizationUtility::translate($key, $extensionName);
    }

    /**
     * Get languages method
     * 
     * Return a array with staticLanguages merged from default language and all defined sys_languages
     * 
     * @return array
     */
    public function getLanguages()
    {
        // Generate languages
        $languages            = [];
        $localeWithoutCharset = GeneralUtility::trimExplode('.', $this->tsSetup['config']['locale_all'])[0];
        $defaultLanguages     = $this->objectManager->get(LanguageRepository::class)->findByCollatingLocale($localeWithoutCharset);
        if ($defaultLanguages->count()) {
            $languages[0] = $defaultLanguages->current();
        }

        $systemLanguages = $this->objectManager->get(SystemLanguageRepository::class)->findAllByUidInList();
        if ($systemLanguages->count()) {
            foreach ($systemLanguages as $systemLanguage) {
                $languages[$systemLanguage->getUid()] = $systemLanguage->getIsoLanguage();
            }
        }

        return $languages;
    }
}
