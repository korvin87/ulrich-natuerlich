<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
/**
 * The repository for Synonyms
 *
 * @author mbruckmoser
 */
class SynonymRepository extends Repository
{

    public function findReverseSynonym($search)
    {
        $query = $this->createQuery();

        $query->matching(
            $query->like('synonym', '%'.$search.'%')
        );

        return $query->execute();
    }
}