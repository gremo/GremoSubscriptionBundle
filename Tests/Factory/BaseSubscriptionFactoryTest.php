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

class BaseSubscriptionFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getInvalidActivationDates
     * @expectedException \Gremo\SubscriptionBundle\Provider\Exception\UnexpectedActivationDateTypeException
     * @expectedExceptionMessage Expected activation date of type DateTime
     */
    public function testExceptionWhenInvalidActivationDate($activationDate)
    {
        $factory  = $this->getMockedFactory();
        $provider = $this->getMockedProvider();

        $factory->expects($this->once())
            ->method('getActivationDateProvider')
            ->will($this->returnValue($provider));

        $provider->expects($this->once())
            ->method('getActivationDate')
            ->will($this->returnValue($activationDate));

        $factory->getSubscription();
    }

    /**
     * @expectedException \Gremo\SubscriptionBundle\Provider\Exception\InvalidActivationDateException
     * @expectedExceptionMessage should not be in the future
     */
    public function testExceptionWhenActivationDateInTheFuture()
    {
        $factory  = $this->getMockedFactory();
        $provider = $this->getMockedProvider();

        $todayDate = new \DateTime();
        $activationDate = clone $todayDate;
        $activationDate->modify('+1 second');

        $factory->expects($this->once())
            ->method('getActivationDateProvider')
            ->will($this->returnValue($provider));

        $factory->expects($this->once())
            ->method('getTodayDate')
            ->will($this->returnValue($todayDate));

        $provider->expects($this->once())
            ->method('getActivationDate')
            ->will($this->returnValue($activationDate));

        $factory->getSubscription();
    }

    public function testSubscriptionWhenActivationDateSameAsToday()
    {
        $factory  = $this->getMockedFactory();
        $provider = $this->getMockedProvider();

        $factory->expects($this->once())
            ->method('getActivationDateProvider')
            ->will($this->returnValue($provider));

        $factory->expects($this->once())
            ->method('getTodayDate')
            ->will($this->returnValue(new \DateTime('2012-09-01')));

        $factory->expects($this->any())
            ->method('getInterval')
            ->will($this->returnValue(new \DateInterval('P30D')));

        $provider->expects($this->once())
            ->method('getActivationDate')
            ->will($this->returnValue(new \DateTime('2012-09-01')));

        /** @var $subscription \Gremo\SubscriptionBundle\Model\BaseSubscription */
        $subscription = $factory->getSubscription();
        $format = 'Y-m-d';

        $this->assertSame(1, count($subscription));
        $this->assertSame('2012-09-01', $subscription->getCurrentPeriod()->getFirstDate()->format($format));
        $this->assertSame('2012-09-30', $subscription->getCurrentPeriod()->getLastDate()->format($format));
    }

    public function testSubscriptionWithOneDayInterval()
    {
        $factory  = $this->getMockedFactory();
        $provider = $this->getMockedProvider();

        $factory->expects($this->once())
            ->method('getActivationDateProvider')
            ->will($this->returnValue($provider));

        $factory->expects($this->once())
            ->method('getTodayDate')
            ->will($this->returnValue(new \DateTime('2012-09-12')));

        $factory->expects($this->any())
            ->method('getInterval')
            ->will($this->returnValue(new \DateInterval('P1D')));

        $provider->expects($this->once())
            ->method('getActivationDate')
            ->will($this->returnValue(new \DateTime('2012-09-01')));

        /** @var $subscription \Gremo\SubscriptionBundle\Model\BaseSubscription */
        $subscription = $factory->getSubscription();
        $format = 'Y-m-d';

        $this->assertSame(12, count($subscription));

        $firstPeriod = $subscription->getFirstPeriod();
        $currentPeriod = $subscription->getCurrentPeriod();

        $this->assertSame('2012-09-01', $firstPeriod->getFirstDate()->format($format));
        $this->assertSame('2012-09-01', $firstPeriod->getLastDate()->format($format));
        $this->assertSame('2012-09-12', $currentPeriod->getFirstDate()->format($format));
        $this->assertSame('2012-09-12', $currentPeriod->getLastDate()->format($format));
    }

    public function testSubscriptionWithManyPeriods()
    {
        $factory  = $this->getMockedFactory();
        $provider = $this->getMockedProvider();

        $factory->expects($this->once())
            ->method('getActivationDateProvider')
            ->will($this->returnValue($provider));

        $provider->expects($this->once())
            ->method('getActivationDate')
            ->will($this->returnValue(new \DateTime('2012-09-01')));

        $factory->expects($this->any())
            ->method('getInterval')
            ->will($this->returnValue(new \DateInterval('P30D')));

        $factory->expects($this->once())
            ->method('getTodayDate')
            ->will($this->returnValue(new \DateTime('2012-12-12')));

        /** @var $subscription \Gremo\SubscriptionBundle\Model\BaseSubscription */
        $subscription = $factory->getSubscription();
        $format = 'Y-m-d';

        $this->assertSame(4, count($subscription));
        $this->assertSame('2012-09-01', $subscription[0]->getFirstDate()->format($format));
        $this->assertSame('2012-09-30', $subscription[0]->getLastDate()->format($format));
        $this->assertSame('2012-10-01', $subscription[1]->getFirstDate()->format($format));
        $this->assertSame('2012-10-30', $subscription[1]->getLastDate()->format($format));
        $this->assertSame('2012-10-31', $subscription[2]->getFirstDate()->format($format));
        $this->assertSame('2012-11-29', $subscription[2]->getLastDate()->format($format));
        $this->assertSame('2012-11-30', $subscription[3]->getFirstDate()->format($format));
        $this->assertSame('2012-12-29', $subscription[3]->getLastDate()->format($format));
    }

    public function getInvalidActivationDates()
    {
        return array(
            array(null),
            array(''),
            array(new \stdClass())
        );
    }

    public function getMockedFactory()
    {
        return $this->getMockBuilder('Gremo\SubscriptionBundle\Factory\BaseSubscriptionFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('getActivationDateProvider', 'getTodayDate', 'getInterval'))
            ->getMock();
    }

    public function getMockedProvider()
    {
        return $this->getMockBuilder('Gremo\SubscriptionBundle\Provider\ActivationDateProviderInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
