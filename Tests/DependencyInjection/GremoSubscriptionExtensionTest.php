<?php

/*
 * This file is part of the GremoSubscriptionBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\SubscriptionBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Gremo\SubscriptionBundle\DependencyInjection\GremoSubscriptionExtension;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class GremoSubscriptionExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;

    /**
     * @var \Gremo\SubscriptionBundle\DependencyInjection\GremoSubscriptionExtension
     */
    private $extension;

    public function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->extension = new GremoSubscriptionExtension();
    }

    public function tearDown()
    {
        unset($this->container, $this->extension);
    }

    /**
     * Interval configuration section is missing
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage child node "interval" at path "gremo_subscription" must be configured
     */
    public function testLoadThrowsExceptionWhenIntervalIsNotSet()
    {
        $config = $this->getValidConfiguration();
        unset($config['gremo_subscription']['interval']);

        $this->extension->load($config, $this->container);
    }

    /**
     * Interval configuration section is empty
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage "gremo_subscription.interval" cannot contain an empty value
     */
    public function testLoadThrowsExceptionWhenIntervalIsNull()
    {
        $config = $this->getValidConfiguration();
        $config['gremo_subscription']['interval'] = null;

        $this->extension->load($config, $this->container);
    }

    /**
     * Interval configuration is invalid
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage interval specification
     */
    public function testLoadThrowsExceptionWhenIntervalIsInvalid()
    {
        $config = $this->getValidConfiguration();
        $config['gremo_subscription']['interval'] = 'foobar';

        $this->extension->load($config, $this->container);
    }

    /**
     * Interval configuration is too short
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage 1 day long
     */
    public function testLoadThrowsExceptionWhenIntervalIsShorterThanOneDay()
    {
        $config = $this->getValidConfiguration();
        $config['gremo_subscription']['interval'] = 'P0DT10M';

        $this->extension->load($config, $this->container);
    }

    /**
     * Activation provider configuration section is missing
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage child node "activation_provider" at path "gremo_subscription" must be configured
     */
    public function testLoadThrowsExceptionWhenActivationProviderIsNotSet()
    {
        $config = $this->getValidConfiguration();
        unset($config['gremo_subscription']['activation_provider']);

        $this->extension->load($config, $this->container);
    }

    /**
     * Activation provider configuration section is empty
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage "gremo_subscription.activation_provider" cannot contain an empty value
     */
    public function testLoadThrowsExceptionWhenActivationProviderIsNull()
    {
        $config = $this->getValidConfiguration();
        $config['gremo_subscription']['activation_provider'] = null;

        $this->extension->load($config, $this->container);
    }

    /**
     * Subscription class parameter is created
     */
    public function testLoadIsLoadingSubscriptionClassParameter()
    {
        $this->extension->load($this->getValidConfiguration(), $this->container);

        $this->assertTrue($this->container->hasParameter('gremo_subscription.class'));
        $this->assertSame($this->container->getParameter('gremo_subscription.class'),
            'Gremo\SubscriptionBundle\Model\BaseSubscription');
    }

    /**
     * Factory class parameter is created
     */
    public function testLoadIsLoadingSubscriptionFactoryClassParameter()
    {
        $this->extension->load($this->getValidConfiguration(), $this->container);

        $this->assertTrue($this->container->hasParameter('gremo_subscription_factory.class'));
        $this->assertSame($this->container->getParameter('gremo_subscription_factory.class'),
            'Gremo\SubscriptionBundle\Factory\BaseSubscriptionFactory');
    }

    /**
     * Container interval parameter is created
     */
    public function testLoadSetsIntervalParameter()
    {
        $config = $this->getValidConfiguration();
        $this->extension->load($config, $this->container);

        $this->assertTrue($this->container->hasParameter('gremo_subscription.interval'));

        $this->assertSame($config['gremo_subscription']['interval'],
            $this->container->getParameter('gremo_subscription.interval'));
    }

    /**
     * Container activation provider parameter is created
     */
    public function testLoadSetsActivationProviderParameter()
    {
        $config = $this->getValidConfiguration();
        $this->extension->load($config, $this->container);

        $this->assertTrue($this->container->hasParameter('gremo_subscription.activation_provider'));

        $this->assertSame($config['gremo_subscription']['activation_provider'],
            $this->container->getParameter('gremo_subscription.activation_provider'));
    }

    /**
     * Factory service definition is created
     *
     * @depends testLoadSetsIntervalParameter
     */
    public function testLoadIsLoadingSubscriptionFactoryService()
    {
        $this->extension->load($this->getValidConfiguration(), $this->container);

        // Definition
        $this->assertTrue($this->container->hasDefinition('gremo_subscription_factory'));

        // Class
        $this->assertSame($this->container->getDefinition('gremo_subscription_factory')->getClass(),
            '%gremo_subscription_factory.class%');

        // Argument
        $this->assertSame('%gremo_subscription.interval%',
            $this->container->getDefinition('gremo_subscription_factory')->getArgument(0));
    }

    /**
     * Subscription service definition is created
     */
    public function testLoadIsLoadingSubscriptionService()
    {
        $this->extension->load($this->getValidConfiguration(), $this->container);

        // Definition
        $this->assertTrue($this->container->hasDefinition('gremo_subscription'));

        // Class
        $this->assertSame($this->container->getDefinition('gremo_subscription')->getClass(),
            '%gremo_subscription.class%');

        // Factory service
        $this->assertSame($this->container->getDefinition('gremo_subscription')->getFactoryService(),
            'gremo_subscription_factory');

        // Factory method
        $this->assertSame($this->container->getDefinition('gremo_subscription')->getFactoryMethod(),
            'getSubscription');
    }

    /**
     * @return array
     */
    public function getValidConfiguration()
    {
        return array(
            'gremo_subscription' => array(
                'interval' => 'P30D',
                'activation_provider' => 'foobar'
            )
        );
    }
}
