<?php

/*
 * This file is part of the GremoSubscriptionBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\SubscriptionBundle\Tests\Factory;

class BaseSubscriptionTest extends \PHPUnit_Framework_TestCase
{

    public function testSubscriptionGetPeriodWhenPeriodDoesNotExist()
    {
        $subscription = $this->getMockedBaseSubscription();
        $period = $this->getMockedBaseSubscriptionPeriod();

        $period->expects($this->once())
            ->method('getFirstDate')
            ->will($this->returnValue(new \DateTime('2012-09-01')));

        $period->expects($this->never())
            ->method('getLastDate')
            ->will($this->returnValue(new \DateTime('2012-09-30')));

        $subscription->expects($this->once())
            ->method('getPeriods')
            ->will($this->returnValue(array($period)));

        $this->assertNull($subscription->getPeriod(new \DateTime('1970-01-01')));
    }

    public function testSubscriptionGetPeriodWhenPeriodExists()
    {
        $subscription = $this->getMockedBaseSubscription();
        $period = $this->getMockedBaseSubscriptionPeriod();

        $period->expects($this->once())
            ->method('getFirstDate')
            ->will($this->returnValue(new \DateTime('2012-09-01')));

        $period->expects($this->once())
            ->method('getLastDate')
            ->will($this->returnValue(new \DateTime('2012-09-30')));

        $subscription->expects($this->once())
            ->method('getPeriods')
            ->will($this->returnValue(array($period)));

        $this->assertSame($period, $subscription->getPeriod(new \DateTime('2012-09-20')));
    }

    public function getMockedBaseSubscription()
    {
        return $this->getMockBuilder('Gremo\SubscriptionBundle\Model\BaseSubscription')
            ->disableOriginalConstructor()
            ->setMethods(array('getPeriods'))
            ->getMock();
    }

    public function getMockedBaseSubscriptionPeriod()
    {
        return $this->getMockBuilder('Gremo\SubscriptionBundle\Model\BaseSubscriptionPeriod')
            ->disableOriginalConstructor()
            ->setMethods(array('getFirstDate', 'getLastDate'))
            ->getMock();
    }
}
