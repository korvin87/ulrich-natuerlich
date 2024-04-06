<?php
/*
 * FormService
 * 
 * @copyright   2016 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\Domain\Service;

use TYPO3\CMS\Core\SingletonInterface;
use Abavo\AbavoForm\Utility\BasicUtility;
use Abavo\AbavoForm\Domain\Repository\FormRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Abavo\AbavoForm\Domain\Model\Form;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * FormService
 *
 * @author mbruckmoser
 */
class FormService implements SingletonInterface
{
    /**
     * BasicUtility
     *
     * @var BasicUtility
     */
    protected $basicUtility = null;

    /**
     * FormRepository
     *
     * @var FormRepository
     */
    protected $formRepository = null;

    /**
     * objectManager
     *
     * @var ObjectManager
     */
    protected $objectManager = null;

    /*
     * @var \DateTime
     */
    protected $dateTime = null;

    /*
     * @var boolean
     */
    protected $successed = false;
    
    /**
     * The PI Settings
     *
     * @var array
     */
    protected $settings = [];

    /**
     * Constructor
     * 
     * @param array $settings
     */
    public function __construct($settings)
    {
        $this->dateTime      = new \DateTime;
        $this->basicUtility  = GeneralUtility::makeInstance(BasicUtility::class);
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->settings = $settings;

        // Init repository
        $formObj = GeneralUtility::makeInstance($settings['formModelClass']);
        $this->formRepository = $this->objectManager->get($formObj->getRepositoryClass());
        $this->formRepository->setDefaultQuerySettings(
            $this->formRepository->createQuery()->getQuerySettings()->setStoragePageIds(GeneralUtility::intExplode(',', $settings['storagePids']))
        );
    }

    /**
     * Run (DUMMY) method
     *
     * @param Form $form
     * @return mixed
     */
    public function run(Form $form)
    {
        return $this;
    }

    public function setSuccessed($successed)
    {
        $this->successed = (boolean) $successed;
    }

    public function isSuccessed()
    {
        return $this->successed;
    }
}