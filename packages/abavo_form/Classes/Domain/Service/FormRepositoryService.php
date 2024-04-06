<?php
/*
 * FormService
 * 
 * @copyright   2016 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\Domain\Service;

use Abavo\AbavoForm\Domain\Model\Form;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Session;
/**
 * FormRepositoryService
 *
 * @author mbruckmoser
 */
class FormRepositoryService extends FormService
{

    /**
     * Run method
     *
     * @param Form $form
     * @return mixed
     */
    public function run(Form $form)
    {
        if ($form instanceof Form) {

            // Set PID
            if ($pid = current($this->formRepository->createQuery()->getQuerySettings()->getStoragePageIds())) {
                $form->setPid($pid);
            }

            // Set DateTime
            $form->setDatetime(new \DateTime);

            // Add/update record to/in repository
            if ($form->isProcessed() && $form->isResetAfterCreate() === false) {

                $persistForm = $this->formRepository->findOneByUniqueId($form->getUniqueId());
                if ($persistForm) {

                    // Set the UID to get the ability to become persistence
                    $form->setUid($persistForm->getUid());

                    // Force reset partner 
                    if ($form->getPartner() === null) {
                        $form->setPartner(0);
                    }

                    // Inject persistence manager
                    $persistenceManager = $this->objectManager->get(PersistenceManager::class);
                    $session            = $this->objectManager->get(Session::class);
                    $session->registerObject($form, $form->getUid());
                    $persistenceManager->injectPersistenceSession($session);
                    $this->formRepository->injectPersistenceManager($persistenceManager);

                    // Update PERSISTENCE record
                    $this->formRepository->update($form);
                }
            } else {
                // Add NEW record
                $this->formRepository->add($form);
            }

            // Set successed
            $this->setSuccessed(true);
        }
    }
}