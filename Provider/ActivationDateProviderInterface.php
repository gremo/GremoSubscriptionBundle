<?php

/*
 * This file is part of the GremoSubscriptionBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\SubscriptionBundle\Provider;

/**
 * Provides the activation day for the subscription
 */
interface ActivationDateProviderInterface
{
    /**
     * @return \DateTime
     */
    public function getActivationDate();
}
