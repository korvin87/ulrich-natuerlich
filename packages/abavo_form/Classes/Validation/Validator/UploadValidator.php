<?php

namespace Abavo\AbavoForm\Validation\Validator;

/*
 * abavo_form
 * 
 * @copyright   2016 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;
use Abavo\AbavoForm\Utility\BasicUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * UploadValidator
 *
 * @author mbruckmoser
 */
class UploadValidator extends AbstractValidator
{
    /**
     * BasicUtility
     *
     * @var BasicUtility
     */
    protected $basicUtility = null;

    /**
     * @var array
     */
    protected $supportedOptions = [
        'maxfilesize' => [null, 'Minimum size for a valid upload', 'integer'],
        'filetypes' => ['*', 'Only specific file types', 'string']
    ];

    /**
     * Is valid method
     *
     * @param array $uploadArray
     * @return boolean
     */
    public function isValid($uploadArray = null)
    {
        $return = true;

        // Basic upload validation
        if ($this->acceptsEmptyValues === false && $uploadArray['error'] !== UPLOAD_ERR_OK) {
            $this->addError($this->codeToMessage($uploadArray['error']), 1_472_461_008);
        }

        // Max file size
        if ($this->options['maxfilesize'] === 'PHP_INI') {
            $valuePhpIni = $this->basicUtility->returnBytes(ini_get('upload_max_filesize'));

            if (is_array($uploadArray) && array_key_exists('size', $uploadArray) && (int) $uploadArray['size'] >= $valuePhpIni) {
                $this->addError(LocalizationUtility::translate('Form.error.maxFileSizeMessage', 'abavo_form'), 1_472_122_578);
                $return = false;
            }
        }

        // File type
        if ($this->options['filetypes'] !== '*') {
            $possiblefileExtensions = GeneralUtility::trimExplode('|', $this->options['filetypes']);
            $fileExtension          = strtolower(GeneralUtility::trimExplode('/', $uploadArray['type'])[1]);

            if ($fileExtension != '' && !in_array($fileExtension, $possiblefileExtensions)) {
                $this->addError(LocalizationUtility::translate('Form.error.fileTypeMessage', 'abavo_form', [implode(', ', $possiblefileExtensions)]), 1_472_125_784);
                $return = false;
            }
        }

        return $return;
    }

    /**
     * Upload error code to message method
     * Forked from http://php.net/manual/de/features.file-upload.errors.php
     *
     * @param int $code
     * @return string
     */
    private function codeToMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

    public function injectBasicUtility(BasicUtility $basicUtility): void
    {
        $this->basicUtility = $basicUtility;
    }
}