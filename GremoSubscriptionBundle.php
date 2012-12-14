<?php

/*
 * This file is part of the GremoSubscriptionBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\SubscriptionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Gremo\SubscriptionBundle\DependencyInjection\Compiler\AddActivationProviderArgumentPass;

class GremoSubscriptionBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddActivationProviderArgumentPass());
    }
}
