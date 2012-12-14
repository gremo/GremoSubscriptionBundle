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

interface SubscriptionInterface
{
    /**
     * @return \DateInterval
     */
    public function getInterval();

    /**
     * @return SubscriptionPeriodInterface
     */
    public function getFirstPeriod();

    /**
     * @return SubscriptionPeriodInterface
     */
    public function getCurrentPeriod();

    /**
     * @return SubscriptionPeriodInterface[]
     */
    public function getPeriods();
}
