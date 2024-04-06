<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\User;

use TYPO3\CMS\Core\SingletonInterface;
/**
 * SearchController
 *
 * Fork from http://stackoverflow.com/questions/17440847/typo3-extbase-set-and-get-values-from-session
 * @author mbruckmoser
 */
class SessionHandler implements SingletonInterface
{
    private $prefixKey = 'tx_abavo_search_';

    /**
     * Returns the object stored in the user´s PHP session
     * @return Object the stored object
     */
    public function restoreFromSession($key)
    {
        $sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->prefixKey.$key);
        return unserialize($sessionData);
    }

    /**
     * Writes an object into the PHP session
     * @param    $object any serializable object to store into the session
     * @return   Tx_EXTNAME_SessionHandler this
     */
    public function writeToSession($object, $key)
    {
        $sessionData = serialize($object);
        $GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefixKey.$key, $sessionData);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
        return $this;
    }

    /**
     * Cleans up the session: removes the stored object from the PHP session
     * @return   Tx_EXTNAME_SessionHandler this
     */
    public function cleanUpSession($key)
    {
        $GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefixKey.$key, NULL);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
        return $this;
    }

    public function setPrefixKey($prefixKey)
    {
        $this->prefixKey = $prefixKey;
    }
}