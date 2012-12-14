<?php

/*
 * This file is part of the GremoSubscriptionBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\SubscriptionBundle\Provider\Exception;

class InvalidActivationDateException extends ActivationProviderException
{
    public function __construct()
    {
        parent::__construct("Activation date is invalid: should not be in the future.");
    }
}
