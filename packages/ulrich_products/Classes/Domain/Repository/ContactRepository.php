<?php
/**
 * ulrich_products - ContactRepository.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 19.06.2018 - 07:53:28
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Abavo\UlrichProducts\Domain\Model\Contact;

/**
 * ContactRepository
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class ContactRepository extends StdRepository
{

    /**
     * Find by uids
     *
     * @param string|integer|array $uids
     * @return ObjectStorage
     */
    public function findByUids($uids = null)
    {
        $result = new ObjectStorage;

        switch (gettype($uids)) {
            case 'string':
                $uids   = GeneralUtility::intExplode(',', $uids);
                break;
            case 'integer':
                $uids[] = $uids;
                break;
            case 'array':
                $uids   = array_map('intval', $uids);
        }

        if (!empty($uids)) {
            foreach ($uids as $uid) {
                if (($contact = $this->findByUid($uid)) instanceof Contact) {
                    $result->attach($contact);
                }
            }
        }

        return $result;
    }
}