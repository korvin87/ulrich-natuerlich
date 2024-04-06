<?php
/**
 * abavo_search - Utility.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 08.06.2018 - 06:49:33
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Api;

use TYPO3\CMS\Core\SingletonInterface;
use Abavo\AbavoSearch\User\ConfigHelper;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Utility class
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class Utility implements SingletonInterface
{

    /**
     * Get JSON View configurations for flexform method
     * 
     * @param array $config
     * @return array
     */
    public function getJsonViewConfigurationsForFlexform(array $config = [])
    {
        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['api']['jsonViewConfigurationClasses']) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['api']['jsonViewConfigurationClasses'] as $configClassName) {
                if (Utility::isJsonViewConfigurationClassValid($configClassName) === true) {
                    $config['items'][] = [
                        0 => $configClassName::getLabel(),
                        1 => $configClassName
                    ];
                }
            }
        }

        return $config;
    }

    /**
     * Get restult template partial names for flexform
     * 
     * @param array $config
     */
    public function getResultTemplatePartialNamesForFlexform(array &$config = [])
    {
        if (isset($config['row']['settings.jsonViewConfigurationClass'])) {
            $jsonViewConfigurationClass = $config['row']['settings.jsonViewConfigurationClass'];
            if (is_array($jsonViewConfigurationClass) && !empty($jsonViewConfigurationClass)) {
                $jsonViewConfigurationClass = current($config['row']['settings.jsonViewConfigurationClass']);
            }
            if (static::isJsonViewConfigurationClassValid($jsonViewConfigurationClass) === true) {
                $indexConfiguration            = GeneralUtility::makeInstance($jsonViewConfigurationClass)->getIndexConfiguration();
                $indexConfigurationPartialName = $indexConfiguration->getResultTemplatePartial();

                $config['items'][] = [
                    0 => $indexConfigurationPartialName.(($indexConfigurationPartialName === IndexConfiguration::RESULT_TEMPLATE_PARTIAL_DEFAULT) ? ' (default)' : ''),
                    1 => $indexConfigurationPartialName
                ];
            }
        }
    }

    /**
     * Register a JSON View configuration method to 
     * $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['api']['jsonViewConfig']
     * 
     * @param string $configClassName
     */
    public static function registerJsonViewConfiguration($configClassName)
    {
        if (Utility::isJsonViewConfigurationClassValid($configClassName) === true) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['api']['jsonViewConfigurationClasses'][] = $configClassName;
        }
    }

    /**
     * Register JSON View configurations from TypoScript Setup path config.tx_abavosearch.api.configurationClasses method
     */
    public static function registerJsonViewConfigurationsFromTypoScriptSetup()
    {
        $config = ConfigHelper::getInstance(['config', 'tx_abavosearch']);
        if (isset($config->setup['api']['configurationClasses']) && !empty($config->setup['api']['configurationClasses'])) {
            foreach ($config->setup['api']['configurationClasses'] as $configClassName) {
                static::registerJsonViewConfiguration($configClassName);
            }
        }
    }

    /**
     * Validate a JSON View configuration method
     * 
     * @param string $configClassName
     * @return boolean
     */
    public static function isJsonViewConfigurationClassValid($configClassName = '')
    {
        $valid = false;
        if (class_exists($configClassName)) {
            $class = new \ReflectionClass($configClassName);
            $valid = (boolean) $class->implementsInterface(ConfigurationInterface::class);
        }
        return $valid;
    }
}