<?php
/*
 * abavo_form
 * 
 * @copyright   2017 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
/**
 * Stepper
 *
 * @author mbruckmoser
 */
class Stepper
{
    /**
     * The first step
     *
     * @var int
     */
    protected $first = 1;

    /**
     * The current step
     *
     * @var int 
     */
    protected $current = 1;

    /**
     * The next step
     *
     * @var int
     */
    protected $next = null;

    /**
     * The previous step
     *
     * @var int
     */
    protected $prev = null;

    /**
     * The last step
     *
     * @var int
     */
    protected $last = 1;

    /**
     * The constructor
     *
     * @param int $maxsteps
     */
    public function __construct(int $maxsteps = 1)
    {
        $this->last = $maxsteps;

        if ($this->last > $this->current) {
            $this->next = $this->current + 1;
        }
    }

    /**
     * Get an instance of this class
     *
     * @param int $maxsteps
     * @return \Abavo\AbavoForm\Domain\Model\Stepper
     */
    public static function getInstance(int $maxsteps = 1)
    {
        return GeneralUtility::makeInstance(self::class, $maxsteps);
    }

    /**
     * Goto step no.
     *
     * @param int $step
     * @return $this
     */
    public function to(int $step)
    {
        if ($step && $this->current !== $step) {
            $calcStep = ($step - $this->current);
            $this->updateByStepCount($calcStep);
        }

        return $this;
    }

    /**
     * update stepper by steps
     *
     * @param int $steps
     * @param string $direction
     */
    public function updateByStepCount(int $steps)
    {
        if ($steps) {
            $calcStep = ($this->current + $steps);

            // current
            if ($calcStep <= $this->first) {
                $this->current = $this->first;
            } elseif ($calcStep >= $this->last) {
                $this->current = $this->last;
            } else {
                $this->current = $calcStep;
            }

            // next
            if ($this->current < $this->last) {
                $this->next = $this->current + 1;
            } else {
                $this->next = null;
            }

            // prev
            if ($this->current > $this->first) {
                $this->prev = $this->current - 1;
            } else {
                $this->prev = null;
            }
        }

        return $this;
    }

    /**
     * Get the first step
     *
     * @return int
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * Get the current step
     * 
     * @return int
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Get the next step
     *
     * @return int
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * Get the prev step
     *
     * @return int
     */
    public function getPrev()
    {
        return $this->prev;
    }

    /**
     * Get the last step
     *
     * @return int
     */
    public function getLast()
    {
        return $this->last;
    }

    /**
     * Is current step the last?
     *
     * @return boolean
     */
    public function isLast()
    {
        return (boolean) ($this->current === $this->last);
    }

    /**
     * Is current step the first?
     *
     * @return boolean
     */
    public function isFirst()
    {
        return (boolean) ($this->current === $this->first);
    }
}