<?php
/**
 * Redirects all email to different recipients.
 * this is an adaption of the official Swift_Plugins_RedirectingPlugin 
 * of Fabien Potencier, 
 *
 * @package    Swift
 * @subpackage Plugins
 * @author     Benjamin Mack
 */
class Swift_Plugins_RedirectPlugin implements Swift_Events_SendListener {

    /**
     * The recipient who will receive all messages.
     *
     * @var mixed
     */
    private $_recipient;

    /**
     * List of regular expression for recipient whitelisting
     */
    private array $_whitelist = [];

    /**
     * Create a new RedirectingPlugin.
     *
     * @param mixed	$recipient
     * @param array  $whitelist
     */
    public function __construct($recipient, array $whitelist = []) {
        $this->setRecipient($recipient);
        $this->_whitelist = $whitelist;
    }

    /**
     * Set the recipient of all messages.
     *
     * @param mixed $recipient
     */
    public function setRecipient($recipient) {
    	if (!is_array($recipient)) {
	    	$this->_recipient = explode(',', $recipient);
    	} else {
	        $this->_recipient = $recipient;
    	}
    }

    /**
     * Get the recipient of all messages.
     *
     * @return array
     */
    public function getRecipient() {
        return $this->_recipient;
    }

    /**
     * Set a list of regular expressions to whitelist certain recipients
     *
     * @param array $whitelist
     */
    public function setWhitelist(array $whitelist)
    {
        $this->_whitelist = $whitelist;
    }

    /**
     * Get the whitelist
     *
     * @return array
     */
    public function getWhitelist()
    {
        return $this->_whitelist;
    }

    /**
     * Invoked immediately before the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt) {
        $message = $evt->getMessage();
        $headers = $message->getHeaders();

        // save current recipients
        $headers->addMailboxHeader('X-Swift-To', $message->getTo());
        $headers->addMailboxHeader('X-Swift-Cc', $message->getCc());
        $headers->addMailboxHeader('X-Swift-Bcc', $message->getBcc());

        // Add hard coded recipient
        foreach ($this->_recipient as $recipient) {
	        $message->addTo($recipient);
        }

        // Filter remaining headers against whitelist
        $this->_filterHeaderSet($headers, 'To');
        $this->_filterHeaderSet($headers, 'Cc');
        $this->_filterHeaderSet($headers, 'Bcc');
    }

    /**
     * Filter header set against a whitelist of regular expressions
     *
     * @param Swift_Mime_HeaderSet $headerSet
     * @param string $type
     */
    private function _filterHeaderSet(Swift_Mime_HeaderSet $headerSet, $type)
    {
        foreach ($headerSet->getAll($type) as $headers) {
            $headers->setNameAddresses($this->_filterNameAddresses($headers->getNameAddresses()));
        }
    }

    /**
     * Filtered list of addresses => name pairs
     *
     * @param array $recipients
     * @return array
     */
    private function _filterNameAddresses(array $recipients)
    {
        $filtered = [];

        foreach ($recipients as $address => $name) {
            if ($this->_isWhitelisted($address)) {
                $filtered[$address] = $name;
            }
        }

        return $filtered;
    }

    /**
     * Matches address against whitelist of regular expressions
     *
     * @param $recipient
     * @return bool
     */
    protected function _isWhitelisted($recipient)
    {
        if (in_array($recipient, $this->_recipient)) {
            return true;
        }

        foreach ($this->_whitelist as $pattern) {
            if (preg_match($pattern, $recipient)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Invoked immediately after the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
        $this->_restoreMessage($evt->getMessage());
    }

    // -- Private methods

    private function _restoreMessage(Swift_Mime_Message $message)
    {
        // restore original headers
        $headers = $message->getHeaders();

        if ($headers->has('X-Swift-To')) {
            $message->setTo($headers->get('X-Swift-To')->getNameAddresses());
            $headers->removeAll('X-Swift-To');
        }

        if ($headers->has('X-Swift-Cc')) {
            $message->setCc($headers->get('X-Swift-Cc')->getNameAddresses());
            $headers->removeAll('X-Swift-Cc');
        }

        if ($headers->has('X-Swift-Bcc')) {
            $message->setBcc($headers->get('X-Swift-Bcc')->getNameAddresses());
            $headers->removeAll('X-Swift-Bcc');
        }
    }
}
