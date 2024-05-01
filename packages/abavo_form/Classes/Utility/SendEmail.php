<?php

namespace Abavo\AbavoForm\Utility;

/**
 * Send Email Helper
 *
 * @copyright   2016 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\MailUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SendEmail helper
 */
class SendEmail implements SingletonInterface
{

    /**
     * @param array $to recipient of the email in the format array('recipient@domain.tld' => 'Recipient Name')
     * @param array $bcc recipient of the email in the format array('recipient@domain.tld' => 'Recipient Name')
     * @param array $sender sender of the email in the format array('sender@domain.tld' => 'Sender Name')
     * @param array $reply reply to email address in the format array('sender@domain.tld' => 'Sender Name')
     * @param string $subject subject of the email
     * @param string $templatePath the template path and filename without file extension egg. EXT:abavo_form/Resources/Private/Templates/Email/Form
     * @param array $variables variables to be passed to the Fluid view
     * @param ControllerContext $context
     * @param string $format Template format default is "html"
     * @param array $attachments E-Mail attachments
     *
     * @return boolean TRUE on success, otherwise false
     */
    public function sendTemplateEmail($to = [], $bcc = [], $sender = [], $reply = [], $subject = '', $tmplPathName = '', $variables = [], $context = null, $format = 'html', $attachments = [])
    {
        // Create StandaloneView
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $emailView     = $objectManager->get(StandaloneView::class);
        ($context != null) ? $emailView->setControllerContext($context) : null;

        // Define Template from TypoScrip-Configuration
        $emailView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($tmplPathName.'.'.$format));

        /*
         * Changed since TYPO3 8.7
         * https://wiki.typo3.org/How_to_use_the_Fluid_Standalone_view_to_render_template_based_emails#Usage_in_TYPO3_8.7
         */
        if (version_compare(GeneralUtility::makeInstance(Typo3Version::class)->getBranch(), '8.7', '>=')) {
            $emailView->setLayoutRootPaths([dirname(GeneralUtility::getFileAbsFileName($tmplPathName)).'/Layouts/']);
            $emailView->setPartialRootPaths([dirname(GeneralUtility::getFileAbsFileName($tmplPathName)).'/Partials/']);
        }

        // Assign variables
        $emailView->setFormat($format);
        $emailView->assignMultiple($variables);

        /*
         *  Generate MailMessage
         */
        $message = GeneralUtility::makeInstance(MailMessage::class);
        $message->setFrom($sender);
        $message->setTo($to);
        //$message->setReturnPath(key(MailUtility::getSystemFrom()));

        // bcc
        if (is_array($bcc) && !empty($bcc)) {
            $message->setBcc($bcc);
        }

        // Reply To
        if (is_array($reply) && !empty($reply)) {
            $message->setReplyTo($reply);
        }

        // Subject/Content
        $message->setSubject($subject);
        $message->html($emailView->render());

        // Possible attachments
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                $message->attach($attachment);
            }
        }

        // Send Mail
        $message->send();
        return $message->isSent();
    }
}