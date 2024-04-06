<?php
/*
 * abavo_search
 * 
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain;

use TYPO3\CMS\Core\SingletonInterface;
use Abavo\AbavoSearch\Domain\Model\Index;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Abavo\AbavoSearch\Domain\Exception\IndexException;
use Abavo\AbavoSearch\Domain\Model\Term;
use Abavo\AbavoSearch\Domain\Repository\TermRepository;

/**
 * TermManager
 *
 * @author mbruckmoser
 */
class TermManager implements SingletonInterface
{
    /**
     * termRepository
     *
     * @var TermRepository
     */
    protected $termRepository = null;

    /**
     * Get terms for autocomplete
     *
     * @param string $term
     * @param int $sysLanguageUid
     * @param string $usergroup
     * @param int $pid
     * @param in $limit
     * @return array
     */
    public function getTermsForAutocomplete($term = '', $sysLanguageUid = 0, $usergroup = '', $pid = 0, $limit = 10)
    {
        $results            = [];
        $registeredServices = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['termServiceClasses'];

        if (!empty($registeredServices)) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

            foreach ($registeredServices as $termServiceClass) {

                $termService = $objectManager->get($termServiceClass);
                if (method_exists($termService, 'findForAutocomplete')) {
                    $serviceResults = $objectManager->get($termServiceClass)->findForAutocomplete(
                        ['term' => $term, 'sysLanguageUid' => $sysLanguageUid, 'usergroup' => $usergroup, 'pid' => $pid]
                    );
                    if (!empty($serviceResults)) {
                        foreach ($serviceResults as $resultItem) {
                            array_push($results, ['search' => $resultItem]);
                        }
                    }
                }
            }
        }

        return array_slice($results, 0, (int) $limit);
    }

    /**
     * Update term records method
     *
     * @param string $searchString whitespace separated searchstring
     * @param string $fegroup Commaseparated fegroup uids
     * @param array $results the search results array
     * @throws IndexException
     */
    public function updateTerms($searchString = '', $fegroup = '0', $results = [])
    {
        if ($searchString == '') {
            throw new IndexException('TermManager: No search value.');
        }

        // If a term does not exisit with current searchstring, FE-Users-Groups and current language (set in repository)
        if (!empty($results) && !$this->termRepository->findForTermManagerDoesTermExist($searchString, $fegroup)) {

            $result = current($results);
            if ($result instanceof Index && (boolean) $result->getRefid()) {

                $tempTerm = GeneralUtility::makeInstance(Term::class);
                $tempTerm->setRefid($result->getRefid());
                $tempTerm->setSearch($searchString);
                $tempTerm->setFegroup($result->getFegroup());
                $tempTerm->setSysLanguageUid($result->getSysLanguageUid());
                $tempTerm->setTstamp(time());
                $tempTerm->setCrdate(time());

                $this->termRepository->add($tempTerm);
            }
        }
    }

    /**
     * Clean unused terms from pid for command controller method
     * 
     * @param int $pid the pid
     * @return boolean
     */
    public static function cleanUnusedTermsFromPidForCommandController($pid = '0')
    {
        $return = false;
        try {
            $objectManager  = GeneralUtility::makeInstance(ObjectManager::class);
            $termRepository = $objectManager->get(TermRepository::class);
            $return         = $termRepository->cleanTermsFormPid($pid);
        } catch (\Exception $e) {
            $return = $e;
        }

        return $return;
    }

    public function injectTermRepository(TermRepository $termRepository): void
    {
        $this->termRepository = $termRepository;
    }
}