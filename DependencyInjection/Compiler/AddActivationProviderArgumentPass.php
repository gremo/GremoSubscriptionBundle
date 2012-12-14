<?php

/*
 * This file is part of the GremoSubscriptionBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\SubscriptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class AddActivationProviderArgumentPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        // Activation provider service id
        $providerId = $container->getParameter('gremo_subscription.activation_provider');

        if(!$container->hasDefinition($providerId)) {
            throw new InvalidArgumentException(sprintf('Activation provider service "%s" not found.', $providerId));
        }

        $providerDefinition = $container->getDefinition($providerId);
        $reflector = new \ReflectionClass($providerDefinition->getClass());
        $interface = 'Gremo\SubscriptionBundle\Provider\ActivationDateProviderInterface';

        if(!$reflector->isSubclassOf($interface)) {
            throw new InvalidArgumentException(sprintf('Activation provider service "%s" must implement "%s".', $providerId, $interface));
        }

        // Add the activation provider argument to the factory definition
        $factoryDefinition = $container->getDefinition('gremo_subscription_factory');
        $factoryDefinition->addArgument(new Reference($providerId));
    }
}
