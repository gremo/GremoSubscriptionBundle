<?php

/*
 * This file is part of the GremoSubscriptionBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\SubscriptionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $tb = new TreeBuilder();
        $rootNode = $tb->root('gremo_subscription');

        $rootNode
            ->children()
                ->scalarNode('interval')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function($i) {
                            try {
                                new \DateInterval($i);

                                return false;
                            }
                            catch(\Exception $e) {
                                return true;
                            }
                        })
                        ->thenInvalid("The interval %s is not a valid interval specification.")
                    ->end()
                    ->validate()
                        ->ifTrue(function($i) {
                            $interval = new \DateInterval($i);

                            return !($interval->d > 0);
                        })
                        ->thenInvalid("The interval %s should be at least 1 day long.")
                    ->end()
                ->end()
                ->scalarNode('activation_provider')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;

        return $tb;
    }
}
