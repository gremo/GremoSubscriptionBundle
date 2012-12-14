<?php

/*
 * This file is part of the GremoSubscriptionBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\SubscriptionBundle\Tests\DependencyInjection\Compiler;
use Gremo\SubscriptionBundle\DependencyInjection\Compiler\AddActivationProviderArgumentPass;

class AddActivationProviderArgumentPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Gremo\SubscriptionBundle\DependencyInjection\Compiler\AddActivationProviderArgumentPass
     */
    private $pass;

    public function setUp()
    {
        $this->pass = new AddActivationProviderArgumentPass();
    }

    public function tearDown()
    {
        unset($this->pass);
    }

    /**
     * Activation provider service definition does not exist
     *
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @expectedExceptionMessage service "foo" not found
     */
    public function testPassThrowsExceptionWhenProviderDefinitionDoesNotExist()
    {
        $container = $this->getMockedContainer();

        $container
            ->expects($this->once())
            ->method('getParameter')
            ->with('gremo_subscription.activation_provider')
            ->will($this->returnValue('foo'));

        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('foo')
            ->will($this->returnValue(false));

        $this->pass->process($container);
    }

    /**
     * Activation provider service definition class does not implement the interface
     *
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @expectedExceptionMessage service "foo" must implement
     */
    public function testPassThrowsExceptionWhenProviderDoesNotImplementInterface()
    {
        $container = $this->getMockedContainer();
        $definition = $this->getMockedDefinition();

        $container
            ->expects($this->once())
            ->method('getParameter')
            ->with('gremo_subscription.activation_provider')
            ->will($this->returnValue('foo'));

        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('foo')
            ->will($this->returnValue(true));

        $container
            ->expects($this->once())
            ->method('getDefinition')
            ->with('foo')
            ->will($this->returnValue($definition));

        $definition
            ->expects($this->once())
            ->method('getClass')
            ->will($this->returnValue('DateTime'));

        $this->pass->process($container);
    }

    /**
     * Activation provider service definition is correct
     */
    public function testPassSetsFactoryServiceDefinition()
    {
        $container = $this->getMockedContainer();
        $factory = $this->getMockedDefinition();
        $definition = $this->getMockedDefinition();

        $container
            ->expects($this->at(0))
            ->method('getParameter')
            ->with('gremo_subscription.activation_provider')
            ->will($this->returnValue('foo'));

        $container
            ->expects($this->at(1))
            ->method('hasDefinition')
            ->with('foo')
            ->will($this->returnValue(true));

        $container
            ->expects($this->at(2))
            ->method('getDefinition')
            ->with('foo')
            ->will($this->returnValue($definition));

        $definition
            ->expects($this->once())
            ->method('getClass')
            ->will($this->returnValue(get_class($this->getMockedActivationProvider())));

        $container->expects($this->at(3))
            ->method('getDefinition')
            ->with('gremo_subscription_factory')
            ->will($this->returnValue($factory));

        $factory->expects($this->once())
            ->method('addArgument')
            ->with($this->logicalAnd(
                $this->isInstanceOf('Symfony\Component\DependencyInjection\Reference'),
                $this->attributeEqualTo('id', 'foo')
            ));

        $this->pass->process($container);


        //$this->markTestIncomplete('Should check the addArgument method arguments.');
    }

    public function getMockedContainer()
    {
        return $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function getMockedDefinition()
    {
        return $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function getMockedActivationProvider()
    {
        return $this->getMockBuilder('Gremo\SubscriptionBundle\Provider\ActivationDateProviderInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
