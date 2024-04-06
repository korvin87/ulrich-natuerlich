<?php
/*
 * abavo_search
 * 
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain;

use TYPO3\CMS\Core\SingletonInterface;
use Abavo\AbavoSearch\Domain\Repository\StatRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Abavo\AbavoSearch\Domain\Model\Stat;
use Abavo\AbavoSearch\Domain\Exception\IndexException;

/**
 * StatManager
 *
 * @author mbruckmoser
 */
class StatManager implements SingletonInterface
{
    /**
     * statRepository
     *
     * @var StatRepository
     */
    protected $statRepository;

    /**
     * UpdateStats Method
     *
     * @param string $searchString The full search string
     * @param int $sysLanguageUid The current sys_language_uid (default=0)
     * @throws IndexException
     */
    public function updateStats($searchString = '', $sysLanguageUid = 0)
    {
        $searchString = trim($searchString);
        if ($searchString === '') {
            throw new IndexException('StatManager: No search value.');
        }

        // STEP 1: check/make/update expression stat record
        $expression = $this->statRepository->findForStatManager($searchString, 'expression', $sysLanguageUid);
        if ((boolean) $expression) {
            $this->updateStat($expression);
        } else {
            $this->addStat('expression', $searchString, $sysLanguageUid);
        }

        // STEP 2: check/make/update term stat records
        $searchTerms = GeneralUtility::trimExplode(' ', $searchString, true);
        foreach ($searchTerms as $searchTerm) {
            $term = $this->statRepository->findForStatManager($searchTerm, 'term', $sysLanguageUid);
            if ((boolean) $term) {
                $this->updateStat($term);
            } else {
                $this->addStat('term', $searchTerm, $sysLanguageUid);
            }
        }
    }

    /**
     * Update Stat Method
     *
     * Increase hits +1 and set tstamp to current timestamp
     * @param Stat $stat
     */
    private function updateStat($stat)
    {
        if (is_object($stat)) {
            $stat->setHits($stat->getHits() + 1);
            $stat->setTstamp(time());
            $this->statRepository->update($stat);
        }
    }

    /**
     * Add a Stat Method
     *
     * @param string $type Stat Record Type ('expression', 'term', 'record')
     * @param string $searchString Search with this in repository
     * @param type $sysLanguageUid sys_language_uid for search expression in repository
     */
    private function addStat($type, $searchString, $sysLanguageUid = 0)
    {
        if ($type != '' && $searchString != '') {
            $tempStat = GeneralUtility::makeInstance(Stat::class);
            $tempStat->setType($type);
            $tempStat->setVal($searchString);
            $tempStat->setHits(1);
            $tempStat->setSysLanguageUid($sysLanguageUid);
            $tempStat->setTstamp(time());
            $tempStat->setCrdate(time());

            $this->statRepository->add($tempStat);
        }
    }

    public function injectStatRepository(StatRepository $statRepository): void
    {
        $this->statRepository = $statRepository;
    }
}