<?php

namespace Abavo\AbavoSearch\Report;

/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
use TYPO3\CMS\Reports\StatusProviderInterface;
use Abavo\AbavoSearch\User\Diagnose;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Provides an abavo_search status report.
 * @author mbruckmoser
 */
class StatusReport implements StatusProviderInterface
{

    /**
     * Returns the status for this report
     *
     * @return array List of statuses
     */
    public function getStatus()
    {
        return GeneralUtility::makeInstance(Diagnose::class)->getStatus();
    }
}
