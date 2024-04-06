<?php
/*
 * SessionService
 * 
 * @copyright   2016 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\Domain\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Crypto\Random;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SessionService
 *
 * @author mbruckmoser
 */
class SessionService implements SingletonInterface
{
    private $prefixKey = 'tx_abavoform_sessionservice_';

    /**
     * Constructor
     */
    public function __construct()
    {
        /*
         *  FIXME: Workaround - cookie expiration date
         *  We set the expiration date of session cookies manual here because the coockie expiration date is set by core-default to "session" if no FE user is logged in.
         */
        if ($GLOBALS['TSFE']->fe_user->user === null) {
            $sessionDataLifetime = (int) ObjectAccess::getProperty($GLOBALS['TSFE']->fe_user, 'sessionDataLifetime');

            switch (GeneralUtility::makeInstance(Typo3Version::class)->getBranch()) {
                case '7.6':
                    $sessionDataTimestamp = (int) ObjectAccess::getProperty($GLOBALS['TSFE']->fe_user, 'sessionDataTimestamp');
                    break;
                case '8.7':
                default:
                    $sessionDataTimestamp = (int) $GLOBALS['TSFE']->fe_user->user['ses_tstamp'];
            }

            setcookie($GLOBALS['TSFE']->fe_user->name, $GLOBALS['TSFE']->fe_user->id, ['expires' => $sessionDataLifetime + $sessionDataTimestamp, 'path' => "/"]);
        }
    }

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
        $GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefixKey.$key, null);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
        return $this;
    }

    public function setPrefixKey($prefixKey)
    {
        $this->prefixKey = $prefixKey;
    }

    public function getUniqueIdentifier()
    {
        return md5(time().GeneralUtility::makeInstance(Random::class)->generateRandomHexString(10));
    }
}