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

class UnexpectedActivationDateTypeException extends ActivationProviderException
{
    public function __construct($value)
    {
        parent::__construct(sprintf('Expected activation date of type DateTime, %s given.', gettype($value)));
    }
}
