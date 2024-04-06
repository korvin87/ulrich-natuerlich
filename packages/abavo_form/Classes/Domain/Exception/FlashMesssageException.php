<?php
/*
 * abavo_form
 * 
 * @copyright   2016 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\Domain\Exception;

use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Messaging\FlashMessage;

/**
 *  FlashMesssageException
 *
 * @author mbruckmoser
 */
class FlashMesssageException extends \Exception
{
    protected $states = [
        AbstractMessage::ERROR,
        AbstractMessage::INFO,
        AbstractMessage::NOTICE,
        AbstractMessage::OK,
        AbstractMessage::WARNING
    ];

    /**
     * FlashMessage Exception Constructor
     *
     * @param string $message
     * @param int $statusCode
     * @param \Throwable $previous
     * @param ActionController $actionController
     * @param string $title The flashmessage title
     */
    public function __construct($message = '', $statusCode = 0, $previous = null, $actionController = null, $title = '')
    {

        // Generate new Exception with flashmessage statuscodes
        $statusCode = $this->getFlashMessageCode($statusCode);
        parent::__construct($message, $statusCode, $previous);

        // Add to flashmessage container
        if ($actionController instanceof ActionController) {
            $title = ((boolean) trim($title)) ? $title : $this->getFile();
            $actionController->addFlashMessage($message, $title, $statusCode);
        }

        // Set header
        $this->setHeaderByStatus($statusCode);
    }

    /**
     * Get status code for flash messages
     *
     * @param int $statusCode
     * @return int
     */
    public function getFlashMessageCode($statusCode = 0)
    {
        $status = 0;

        if (!in_array($statusCode, $this->states)) {
            if ((int) $statusCode > AbstractMessage::ERROR) {
                $status = AbstractMessage::ERROR;
            }
            if ((int) $statusCode < AbstractMessage::NOTICE) {
                $status = AbstractMessage::NOTICE;
            }
        } else {
            $status = $statusCode;
        }

        if ($status == 0) {
            $status = AbstractMessage::ERROR;
        }

        return $status;
    }

    /**
     * Set header by status
     *
     * @param type $statusCode
     */
    private function setHeaderByStatus($statusCode = 0)
    {
        if ((int) $statusCode >= AbstractMessage::ERROR) {
            header('HTTP/1.1 500 Internal Server Error');
        }
        if ((int) $statusCode <= AbstractMessage::NOTICE) {
            header("HTTP/1.1 200 OK");
        }
    }
}