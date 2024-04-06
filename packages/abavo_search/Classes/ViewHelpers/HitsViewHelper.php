<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Abavo\AbavoSearch\Domain\Repository\StatRepository;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
/**
 * ViewHelper to render Hits
 *
 * @author mbruckmoser
 */
class HitsViewHelper extends AbstractViewHelper
{
    /**
     * statRepository
     *
     * @var StatRepository
     */
    protected $statRepository;

    /**
     * Initialize arguments.
     *
     * @throws Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('refid', 'string', 'The refid', false, '');
    }

    /**
     * Render Method
     * 
     * @return int $hits The number of hits from stat
     */
    public function render()
    {
        if ($this->arguments['refid'] != '') {
            $stat = $this->statRepository->findOneByRefid($this->arguments['refid']);
            if ((boolean) $stat) {
                $return = ((int) $stat->getHits()) ?: 0;
            } else {
                $return = 0;
            }
        } else {
            $return = 0;
        }

        return $return;
    }

    public function injectStatRepository(StatRepository $statRepository): void
    {
        $this->statRepository = $statRepository;
    }
}