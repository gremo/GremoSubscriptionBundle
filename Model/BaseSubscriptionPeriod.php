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
 * Represent a subscription period
 */
class BaseSubscriptionPeriod extends \DatePeriod implements SubscriptionPeriodInterface
{
    /**
     * @var \DateTime
     */
    private $firstDate;

    /**
     * @var \DateTime
     */
    private $lastDate;

    /**
     * Get the first day of the subscription period
     *
     * @return \DateTime
     */
    public function getFirstDate()
    {
        return $this->firstDate;
    }

    /**
     * Get the last day of the subscription period
     *
     * @return \DateTime
     */
    public function getLastDate()
    {
        return $this->lastDate;
    }
}
