<?php
/**
 * abavo_search - TermService.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 06.06.2018 - 09:00:54
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Abavo\AbavoSearch\Domain\Repository\TermRepository;
use Abavo\AbavoSearch\Domain\Model\AutocompleteItem;

/**
 * TermService
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class TermService implements SingletonInterface
{

    /**
     * Find for autocomplete method
     * 
     * @param array $conf
     * @return array
     */
    public function findForAutocomplete($conf = [])
    {
        if ($terms = GeneralUtility::makeInstance(ObjectManager::class)->get(TermRepository::class)->findForAutocomplete(
            $conf['term'], $conf['sysLanguageUid'], $conf['usergroup'], $conf['pid'])
        ) {

            $results = [];
            foreach ($terms as $i => $term) {
                $results[$i] = new AutocompleteItem($term['search']);
            }

            return $results;
        }
    }
}