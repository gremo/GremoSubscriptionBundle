<?php

/*
 * This file is part of the GremoSubscriptionBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\SubscriptionBundle\Model;

/**
 * Represents a subscription
 */
class BaseSubscription implements \Iterator, \ArrayAccess, \Countable, SubscriptionInterface
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var \DateInterval
     */
    private $interval;

    /**
     * @var BaseSubscriptionPeriod[]
     */
    private $periods;

    public function __construct()
    {
        $this->index = 0;
        $this->periods = array();
    }

    public function current()
    {
        return $this->periods[$this->index];
    }

    public function key()
    {
        return $this->index;
    }

    public function next()
    {
        ++$this->index;
    }

    public function rewind()
    {
        $this->index = 0;
    }

    public function valid()
    {
        return isset($this->periods[$this->index]);
    }

    public function offsetExists($offset)
    {
        return isset($this->periods[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->periods[$offset]) ? $this->periods[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->periods[] = $value;
        } else {
            $this->periods[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->periods[$offset]);
    }

    public function count()
    {
        return count($this->periods);
    }

    /**
     * Get the subscription interval
     *
     * @return \DateInterval
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Get the first period of the subscription
     *
     * @return BaseSubscriptionPeriod
     */
    public function getFirstPeriod()
    {
        return $this->offsetGet(0);
    }

    /**
     * Get the current period of the subscription
     *
     * @return BaseSubscriptionPeriod
     */
    public function getCurrentPeriod()
    {
        return $this->offsetGet(count($this) - 1);
    }

    /**
     * Get the subscription period that belongs to a given date
     *
     * @param \DateTime $date
     * @return null|BaseSubscriptionPeriod
     */
    public function getPeriod(\DateTime $date)
    {
        foreach($this->getPeriods() as $period) {
            if($date >= $period->getFirstDate() && $date <= $period->getLastDate()) {
                return $period;
            }
        }

        return null;
    }

    /**
     * Get all periods of the subscription
     *
     * @return BaseSubscriptionPeriod[]
     */
    public function getPeriods()
    {
        return $this->periods;
    }
}
