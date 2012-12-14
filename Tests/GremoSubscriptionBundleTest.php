<?php

/*
 * This file is part of the GremoSubscriptionBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\SubscriptionBundle\Tests;

use Gremo\SubscriptionBundle\GremoSubscriptionBundle;

class GremoSubscriptionBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Gremo\SubscriptionBundle\GremoSubscriptionBundle
     */
    private $bundle;

    public function setUp()
    {
        $this->bundle = new GremoSubscriptionBundle();
    }

    public function tearDown()
    {
        unset($this->bundle);
    }

    public function testBundleUseAddActivationProviderArgumentPass()
    {
        $container = $this->getMockedContainerBuilder();

        $container->expects($this->once())
            ->method('addCompilerPass')
            ->with($this->isInstanceOf('Gremo\SubscriptionBundle\DependencyInjection\Compiler\AddActivationProviderArgumentPass'));

        $this->bundle->build($container);
    }

    public function getMockedContainerBuilder()
    {
        return $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
