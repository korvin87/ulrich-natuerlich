<?php
/*
 * abavo_search
 * 
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain;

use TYPO3\CMS\Core\SingletonInterface;
use Abavo\AbavoSearch\Domain\Repository\SynonymRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use Abavo\AbavoSearch\Domain\Exception\IndexException;

/**
 * SynonymManager
 *
 * @author mbruckmoser
 */
class SynonymManager implements SingletonInterface
{
    /**
     * synonymRepository
     *
     * @var SynonymRepository
     */
    protected $synonymRepository = NULL;

    public function addSynonymsToSearch(&$search, &$synonyms, &$synonymReplacements)
    {
        try {
            // Get Synonyms for search string
            $searchWords = GeneralUtility::trimExplode(' ', $search, true);
            foreach ($searchWords as $searchWord) {

                // Get Reverses Synonym (Title or Alt of Synonym-Record)
                $reverseSynonyms = array_merge(
                    $this->synonymRepository->findReverseSynonym($searchWord)->toArray(), $this->synonymRepository->findByAlt($searchWord)->toArray()
                );
                foreach ($reverseSynonyms as $synonym) {

                    // Replacement check
                    if ((boolean) $synonym->getAlt()) {
                        $term                                      = $synonym->getAlt();
                        $synonymReplacements[$synonym->getTitle()] = $synonym->getAlt();
                    } else {
                        $term = $synonym->getTitle();
                    }

                    // Adding term
                    $synonyms = array_merge($synonyms, [$term]);
                    if (strpos($search, (string) $term) === false) {
                        $search .= ' '.$term;
                    }
                }

                // Get Synonyms for each search word
                $synonym = $this->synonymRepository->findOneByTitle($searchWord); // Titles are unique in DB
                if ($synonym) {

                    // Get synonym-list
                    $tempSynonyms = GeneralUtility::trimExplode("\n", $synonym->getSynonym(), true);

                    // Push alt title
                    if ((boolean) $synonym->getAlt()) {
                        array_push($tempSynonyms, $synonym->getAlt());
                        $synonymReplacements[$synonym->getTitle()] = $synonym->getAlt();
                    }

                    // Work synonyms
                    if (!empty($tempSynonyms)) {

                        //Clean synonyms from search string
                        foreach ($searchWords as $searchWordinner) {
                            if ($key = array_search($searchWordinner, $tempSynonyms)) {
                                unset($tempSynonyms[$key]);
                            }
                        }

                        // Add Synonyms
                        $synonyms = array_merge($synonyms, $tempSynonyms);
                        $search .= ' '.implode(' ', $tempSynonyms);
                    }
                }
            }
        } catch (\Exception $e) {
            DebuggerUtility::var_dump($e->getMessage(), 'abavo_search Error in SynonymManager::addSynonymsToSearch');
            return false;
        }
    }

    public function injectSynonymRepository(SynonymRepository $synonymRepository): void
    {
        $this->synonymRepository = $synonymRepository;
    }
}