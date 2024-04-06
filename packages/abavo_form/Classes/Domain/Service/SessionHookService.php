<?php
/*
 * abavo_form
 * 
 * @copyright   2017 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\Domain\Service;

use TYPO3\CMS\Core\SingletonInterface;
use Abavo\AbavoForm\Domain\Model\Form;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SessionHookExample is a light weight example to make a session hook for EXT:abavo_form
 *
 * Register your own hook via namespace like this $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['sessionhooks'][] =  'Abavo\AbavoForm\Hooks\SessionHookExample';
 *
 * @author mbruckmoser
 */
class SessionHookService implements SingletonInterface
{
    /**
     * The form object
     *
     * @var Form
     */
    protected $formObj = null;

    /**
     * Is this class processed?
     *
     * @var boolean
     */
    protected $isProcessed = false;

    /**
     * The registered hook services
     */
    private array $registeredServices = [];

    /**
     * Get a instance of this class
     *
     * @param Form $formObj
     * @param array
     * @return \Abavo\AbavoForm\Domain\Service\SessionHookService
     */
    public static function getInstance(&$formObj = null, &$settings = [])
    {
        return GeneralUtility::makeInstance(static::class)->setFormObj($formObj)->main($settings);
    }

    /**
     * Set the form object
     *
     * @param Form $formObj
     */
    protected function setFormObj($formObj = null)
    {
        $this->formObj = $formObj;
        return $this;
    }

    /**
     * Get the form object
     *
     * return \Abavo\AbavoForm\Domain\Model\Form $formObj
     */
    public function getFormObj()
    {
        return $this->formObj;
    }

    /**
     * Main method
     */
    public function main(&$settings = [])
    {
        // init form object

        if (!$this->formObj instanceof Form) {
            throw new \Exception(__METHOD__.': Form object is not a instance of \Abavo\AbavoForm\Domain\Model\Form');
        }

        // init session hooks
        if (isset($settings['sessionHooks'])) {

            $sessionHooks = GeneralUtility::trimExplode(',', $settings['sessionHooks'], true);

            // work each hook
            if (!empty($sessionHooks)) {
                foreach ($sessionHooks as $hookClass) {

                    if (method_exists($hookClass, 'getInstance') && method_exists($hookClass, 'main')) {
                        $this->registeredServices[] = $hookClass::getInstance($this->formObj, $settings);
                    } else {
                        throw new \Exception(__METHOD__.': No getInstance or main method defined in '.html_entity_decode($hookClass));
                    }
                }
            }
        }

        // set processed
        $this->isProcessed = true;
        return $this;
    }
}