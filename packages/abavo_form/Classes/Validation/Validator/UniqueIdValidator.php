<?php
/**
 * Sigg-Fahrzeugbau - UniqueIdValidator.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 14.06.2018 - 08:52:02
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\AbavoForm\Validation\Validator;

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;
use Abavo\AbavoForm\Domain\Repository\FormRepository;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use Abavo\AbavoForm\Domain\Model\Form;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Abavo\AbavoForm\Domain;

/**
 * UniqueIdValidator
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class UniqueIdValidator extends AbstractValidator
{
    /**
     * @var array
     */
    protected $supportedOptions = [];

    /**
     * is valid method
     * 
     * @param string $uniqueId
     * @return boolean
     */
    protected function isValid($uniqueId)
    {
        $inquireRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(FormRepository::class);
        $inquireRepository->setDefaultQuerySettings($inquireRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false));
        /*

        if (PasswordHashFactory::getSaltingInstance(NULL)->isValidSalt($uniqueId) === false || (boolean) ($inquireRepository->findOneByUniqueId($uniqueId) instanceof Form) === true) {
            $this->addError($this->translateErrorMessage('Validation.uniqueId.notvalid', 'AbavoForm'), 1_528_898_446);
        }
        */
    }
}