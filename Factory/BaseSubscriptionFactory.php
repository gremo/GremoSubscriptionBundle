<?php

/*
 * This file is part of the GremoSubscriptionBundle package.
 *
 * (c) Marco Polichetti <gremo1982@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gremo\SubscriptionBundle\Factory;

use Gremo\SubscriptionBundle\Provider\ActivationDateProviderInterface;
use Gremo\SubscriptionBundle\Provider\Exception\InvalidActivationDateException;
use Gremo\SubscriptionBundle\Provider\Exception\UnexpectedActivationDateTypeException;

class BaseSubscriptionFactory implements SubscriptionFactoryInterface
{
    /**
     * @var \DateInterval
     */
    private $interval;

    /**
     * @var \DateTime
     */
    private $todayDate;

    /**
     * @var \Gremo\SubscriptionBundle\Provider\ActivationDateProviderInterface
     */
    private $provider;

    /**
     * @param string $intervalSpecification
     * @param \Gremo\SubscriptionBundle\Provider\ActivationDateProviderInterface $provider
     */
    public function __construct($intervalSpecification, ActivationDateProviderInterface $provider)
    {
        $this->interval  = new \DateInterval($intervalSpecification);
        $this->todayDate = new \DateTime();
        $this->provider  = $provider;
    }

    /**
     * @return \DateInterval
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @return \DateTime
     */
    public function getTodayDate()
    {
        return $this->todayDate;
    }

    /**
     * @return \Gremo\SubscriptionBundle\Provider\ActivationDateProviderInterface
     */
    public function getActivationDateProvider()
    {
        return $this->provider;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscription()
    {
        /** @var $properties \ReflectionProperty[] */
        $properties = array();
        $reflection = $this->getBaseSubscriptionReflection();

        // Compute property values
        $values = array();

        $values['interval'] = $this->getInterval();
        $values['periods']  = $this->getSubscriptionPeriods();

        // Create a new subscription instance
        $subscription = $reflection->newInstance();

        // Set subscription property value
        foreach($values as $propertyName => $propertyValue) {
            $property = $reflection->getProperty($propertyName);
            $property->setAccessible(true);
            $property->setValue($subscription, $propertyValue);

            $properties[] = $property;
        }

        // Reset reflection properties access
        foreach($properties as $property) {
            $property->setAccessible(false);
        }

        return $subscription;
    }

    /**
     * @return \Gremo\SubscriptionBundle\Model\SubscriptionPeriodInterface[]
     * @throws \Gremo\SubscriptionBundle\Provider\Exception\InvalidActivationDateException
     * @throws \Gremo\SubscriptionBundle\Provider\Exception\UnexpectedActivationDateTypeException
     */
    public function getSubscriptionPeriods()
    {
        $beginDate = $this->getActivationDateProvider()->getActivationDate();
        $todayDate = $this->getTodayDate();

        // Check activation date type
        if(null === $beginDate || (!$beginDate instanceof \DateTime)) {
            throw new UnexpectedActivationDateTypeException($beginDate);
        }

        // Check for activation date in the future
        if($beginDate > $todayDate) {
            throw new InvalidActivationDateException();
        }

        /** @var $properties \ReflectionProperty[] */
        $properties = array();
        $reflection = $this->getBaseSubscriptionPeriodReflection();

        $propertyNames = array('firstDate', 'lastDate');

        // Make subscription period properties accessible
        foreach($propertyNames as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            $property->setAccessible(true);

            $properties[$propertyName] = $property;
        }

        // Create periods
        $periods   = array();
        $startDate = clone $beginDate;

        while($startDate <= $todayDate) {
            // Get the end date
            $endDate = clone $startDate;
            $endDate->add($this->getInterval());

            /** @var $period \Gremo\SubscriptionBundle\Model\BaseSubscriptionPeriod */
            $period = $reflection->newInstanceArgs(array($startDate, new \DateInterval('P1D'), $endDate));

            // Set instance properties
            $dates = iterator_to_array($period);

            $properties['firstDate']->setValue($period, $dates[0]);
            $properties['lastDate']->setValue($period, $dates[count($dates) - 1]);

            // Next period
            $periods[] = $period;
            $startDate->add($this->getInterval());
        }

        // Reset reflection properties access
        foreach($properties as $property) {
            $property->setAccessible(false);
        }

        return $periods;
    }

    /**
     * @return \ReflectionClass
     */
    protected function getBaseSubscriptionReflection()
    {
        return new \ReflectionClass('Gremo\SubscriptionBundle\Model\BaseSubscription');
    }

    /**
     * @return \ReflectionClass
     */
    protected function getBaseSubscriptionPeriodReflection()
    {
        return new \ReflectionClass('Gremo\SubscriptionBundle\Model\BaseSubscriptionPeriod');
    }
}
