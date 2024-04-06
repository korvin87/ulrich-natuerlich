<?php
/*
 * UlrichProducts
 *
 * @copyright   2017 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

namespace Abavo\UlrichProducts\Utility;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * ConfigHelper
 */
class ConfigHelper implements SingletonInterface
{

    use GetInstanceStaticTrait;
    /**
     * The PlugIn setup
     *
     * @var array
     */
    public $setup = null;

    /**
     * Constructor
     *
     * @param string $type The type=key in CONFIGURATION_TYPE_FULL_TYPOSCRIPT array
     * @param string $pluginName The plugin name=key in TS-Setup type array; Take a look in ext_localconf.php for ExtensionUtility::configurePlugin('Abavo.'.$_EXTKEY, 'Pi'....
     */
    public function __construct($type = 'plugin', $pluginName = 'Pi')
    {
        // Get plugIn signatur
        $plugInSignatur = 'tx_'.str_replace('_', '', GeneralUtility::camelCaseToLowerCaseUnderscored(GeneralUtility::trimExplode('\\', __NAMESPACE__)[1])).'_'.strtolower($pluginName);

        // Get typoscript settings
        $objectManager        = GeneralUtility::makeInstance(ObjectManager::class);
        $configurationManager = $objectManager->get(ConfigurationManager::class);
        $ts                   = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $typoScriptService    = $objectManager->get(TypoScriptService::class);
        $config               = $typoScriptService->convertTypoScriptArrayToPlainArray($ts);

        if ($config[$type][$plugInSignatur]) {
            $this->setup = $config[$type][$plugInSignatur];
        } else {
            $this->setup = $config;
        }
    }
}