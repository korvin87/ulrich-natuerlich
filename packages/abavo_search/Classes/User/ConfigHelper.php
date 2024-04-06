<?php
/*
 * AbavoSearch
 *
 * @copyright   2018 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\User;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * ConfigHelper
 */
class ConfigHelper
{
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
    public function __construct($type = null, $pluginName = null)
    {
        // Get plugIn signatur
        $plugInSignatur = 'tx_'.str_replace('_', '', GeneralUtility::camelCaseToLowerCaseUnderscored(GeneralUtility::trimExplode('\\', __NAMESPACE__)[1])).'_'.strtolower($pluginName);

        // Get typoscript settings
        $objectManager        = GeneralUtility::makeInstance(ObjectManager::class);
        $configurationManager = $objectManager->get(ConfigurationManager::class);
        $tsArray              = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

        if (is_array($tsArray)) {
            $config = $objectManager->get(TypoScriptService::class)->convertTypoScriptArrayToPlainArray($tsArray);

            if (isset($config[$type])) {
                if (isset($config[$type][$plugInSignatur])) {
                    $this->setup = $config[$type][$plugInSignatur];
                } else if (isset($config[$type][$pluginName])) {
                    $this->setup = $config[$type][$pluginName];
                } else {
                    $this->setup = $config[$type];
                }
            } else {
                $this->setup = $config;
            }
        }
    }

    /**
     * Get a instance of this class
     * 
     * @return static::class
     */
    public static function getInstance($arguments = [])
    {
        return call_user_func_array([GeneralUtility::class, 'makeInstance'], array_merge([static::class], $arguments));
    }
}