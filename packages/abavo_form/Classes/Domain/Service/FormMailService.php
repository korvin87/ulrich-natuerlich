<?php
/*
 * FormService
 * 
 * @copyright   2016 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\Domain\Service;

use Abavo\AbavoForm\Utility\SendEmail;
use TYPO3\CMS\Core\Utility\MailUtility;
use Abavo\AbavoForm\Domain\Model\Form;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * FormMailService
 *
 * @author mbruckmoser
 */
class FormMailService extends FormService
{
    protected $emailTemplate    = 'EXT:abavo_form/Resources/Private/Templates/Email/Form';
    protected $toAddresses      = ['dev@abavo.de'];
    protected $bccAddresses     = [];
    protected $replyToAddresses = [];
    protected $subject          = 'FormMailService';
    protected $styleSheet       = 'EXT:abavo_form/Resources/Public/Css/Email.css';

    /**
     * SendEmail
     *
     * @var SendEmail
     */
    protected $sendEmailUtility;

    /**
     * Constructor
     */
    public function __construct($settings)
    {
        parent::__construct($settings);

        // Init utilities
        $this->sendEmailUtility = GeneralUtility::makeInstance(SendEmail::class);

        // Define settings
        if ((boolean) $settings['template']['email']) {
            $this->emailTemplate = $settings['template']['email'];
        }
        if ((boolean) $settings['toAddresses']) {
            $this->toAddresses = GeneralUtility::trimExplode(',', $settings['toAddresses']);
        }
        if ((boolean) $settings['bccAddresses']) {
            $this->bccAddresses = GeneralUtility::trimExplode(',', $settings['bccAddresses']);
        }
        if ((boolean) $settings['replyToAddresses']) {
            $this->replyToAddresses = GeneralUtility::trimExplode(',', $settings['replyToAddresses']);
        }
        if (empty($this->replyToAddresses)) {
            $this->replyToAddresses = MailUtility::getSystemFrom();
        }
        if ((boolean) $settings['subject']) {
            $this->subject = $settings['subject'];
        }
        if ((boolean) $settings['styleSheet']) {
            $this->styleSheet = $settings['styleSheet'];
        }
    }

    /**
     * Run method
     *
     * @param Form $form
     */
    public function run(Form $form)
    {
        // Add $form to variables
        $variables = [
            'form' => $form,
            'settings' => $this->settings
        ];
        $variables = [...$variables, 'style' => file_get_contents(GeneralUtility::getFileAbsFileName($this->styleSheet))];

        // Execute email sending (default)
        $isEmailSend = $this->sendEmailUtility->sendTemplateEmail(
            $this->toAddresses, // TO E-Mail Adresses
            $this->bccAddresses, // BCC  E-Mail Adresses
            MailUtility::getSystemFrom(), // FROM E-Mail Adress (INSTALL_TOOL)
            $this->replyToAddresses, // replyto E-Mail Adresses
            $this->subject, // E-MAIL subject
            $this->emailTemplate, // FLUID Template
            $variables // template variables
        );

        // If the default email sending was successed, we sending optionaly the additional customer email
        if ($isEmailSend && (boolean) $this->settings['useFormsEmailAddress'] && filter_var($form->getEmail(), FILTER_VALIDATE_EMAIL)) {

            $isEmailSend = $this->sendEmailUtility->sendTemplateEmail(
                [$form->getEmail() => trim($form->getFirstname().' '.$form->getLastname())], // TO E-Mail Adresses
                $this->bccAddresses, // BCC  E-Mail Adresses
                MailUtility::getSystemFrom(), //FROM E-Mail Adress (INSTALL_TOOL)
                $this->replyToAddresses, // replyto E-Mail Adresses
                (trim($this->settings['customerEmailSubject'])) ? $this->settings['customerEmailSubject'] : $this->subject, // E-MAIL subject
                $this->emailTemplate, // FLUID Template
                [...$variables, 'isCustomerEmail' => 1] // template variables
            );
        }

        // Set successed
        $this->setSuccessed($isEmailSend);
    }
}