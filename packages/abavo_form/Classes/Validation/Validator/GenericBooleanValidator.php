<?php
/*
 * abavo_form
 * 
 * @copyright   2017 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\Validation\Validator;

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
/**
 * GenericBooleanValidator
 *
 * Based on \TYPO3\CMS\Extbase\Validation\Validator\BooleanValidator;
 * With optional message output
 *
 * @author mbruckmoser
 */
class GenericBooleanValidator extends AbstractValidator
{
    /**
     * @var array
     */
    protected $supportedOptions = [
        // The default is set to NULL here, because we need to be backward compatible here, because this
        // BooleanValidator is called automatically on boolean action arguments. If we would set it to TRUE,
        // every FALSE value for an action argument would break.
        'is' => [null, 'Boolean value', 'boolean|string|integer'],
        'notTrueMessage' => [null, 'The message if expectation is not true; Sepearated by colon <TRANSLATION_KEY>:<EXTENSIONNAME>', 'string'],
        'notFalseMessage' => [null, 'The message if expectation is not false; Sepearated by colon <TRANSLATION_KEY>:<EXTENSIONNAME>', 'string']
    ];

    /**
     * Check if $value matches the expectation given to the validator.
     * If it does not match, the function adds an error to the result.
     *
     * Also testing for '1' (true), '0' and '' (false) because casting varies between
     * tests and actual usage. This makes the validator loose but still keeping functionality.
     *
     * @param mixed $value The value that should be validated
     * @return void
     */
    public function isValid($value)
    {
        // see comment above, check if expectation is NULL, then nothing to do!
        if ($this->options['is'] === null) {
            return;
        }
        switch (strtolower((string) $this->options['is'])) {
            case 'true':
            case '1':
                $expectation = true;
                break;
            case 'false':
            case '':
            case '0':
                $expectation = false;
                break;
            default:
                $this->addError('The given expectation is not valid.', 1_361_959_227);
                return;
        }

        // Get message config
        $notTrueMessage  = GeneralUtility::trimExplode(':', $this->options['notTrueMessage'], true);
        $notFalseMessage = GeneralUtility::trimExplode(':', $this->options['notFalseMessage'], true);

        $messageExt = 'extbase';

        if ($value !== $expectation) {
            if (!is_bool($value)) {

                $messageKey = 'validator.boolean.nottrue';

                if (!empty($notTrueMessage) && count($notTrueMessage) === 2) {
                    $messageKey = $notTrueMessage[1];
                    $messageExt = $notTrueMessage[0];
                }

                $this->addError($this->translateErrorMessage($messageKey, $messageExt), 1_361_959_230);
            } else {

                if ($expectation) {

                    $messageKey = 'validator.boolean.nottrue';

                    if (!empty($notTrueMessage) && count($notTrueMessage) === 2) {
                        $messageKey = $notTrueMessage[1];
                        $messageExt = $notTrueMessage[0];
                    }

                    $this->addError($this->translateErrorMessage($messageKey, $messageExt), 1_361_959_230);
                } else {

                    $messageKey = 'validator.boolean.notfalse';

                    if (!empty($notFalseMessage) && count($notFalseMessage) === 2) {
                        $messageKey = $notFalseMessage[1];
                        $messageExt = $notFalseMessage[0];
                    }

                    $this->addError($this->translateErrorMessage($messageKey, $messageExt), 1_361_959_229);
                }
            }
        }
    }
}