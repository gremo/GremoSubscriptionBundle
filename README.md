# GremoSubscriptionBundle [![Build Status](https://secure.travis-ci.org/gremo/GremoSubscriptionBundle.png)](http://travis-ci.org/gremo/GremoSubscriptionBundle)

Symfony2 Bundle for managing subscriptions.

## Installation

Add the following to your `deps` file (for Symfony 2.0.*):

```
[GremoSubscriptionBundle]
    git=https://github.com/gremo/GremoSubscriptionBundle.git
    target=bundles/Gremo/SubscriptionBundle
```

Then register the namespaces with the autoloader (`app/autoload.php`):

```php
$loader->registerNamespaces(array(
    // ...
    'Gremo' => __DIR__.'/../vendor/bundles',
    // ...
));
```

Or, if you are using Composer and Symfony 2.1.*, add to `composer.json` file:

```javascript
{
    "require": {
        "gremo/subscription-bundle": "*"
    }
}
```

Finally register the bundle with your kernel in `app/appKernel.php`:
```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Gremo\SubscriptionBundle\GremoSubscriptionBundle(),
        // ...
    );

    // ...
}
```

## Configuration
Bundle configuration is simple: you first need to specify an interval for the subscription periods. Use the format of
`\DateInterval` [interval specification](http://php.net/manual/en/dateinterval.construct.php). For example, `P30D`, that
is 30 days. Interval should be, at least, one day long.

Then implement `Gremo\SubscriptionBundle\Provider\ActivationDateProviderInterface`, in order to provide an activation date.
Make this class a service and set its name as the `activation_provider` in the configuration:

```
gremo_subscription:
    interval: P30D
    activation_provider: my_activation_provider
```

### Activation date provider example
An example activation date provider, where activation date is the current logged user creation date:

```php
use Gremo\SubscriptionBundle\Provider\ActivationDateProviderInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @DI\Service("my_activation_provider")
 */
class MyActivationProvider implements ActivationDateProviderInterface
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    private $context;

    /**
     * @DI\InjectParams({"context" = @DI\Inject("security.context")})
     */
    public function __construct(SecurityContext $context)
    {
        $this->context = $context;
    }

    /**
     * @return \DateTime
     */
    public function getActivationDate()
    {
        $user = $this->context->getToken()->getUser();

        return $user->getCreatedAt();
    }
}
```

## Usage
You can access the subscription service using the service container, for example in your controller code:
```php
$subscription = $this->get('gremo_subscription'):
```

Say that today is 2012-12-12, interval is 30 days and activation date is 2012-09-01. Periods will be:
- From 2012-09-01 to 2012-09-30 inclusive, the first period
- From 2012-10-01 to 2012-10-30 inclusive
- From 2012-10-31 to 2012-11-29 inclusive
- From 2012-11-30 to 2012-12-29 inclusive, that is the current period

Access the current period from subscription:

```php
$currentPeriod = $subscription->getCurrentPeriod(); // Period from 2012-11-30 to 2012-12-29

$firstDate = $currentPeriod->getFirstDate(); // DateTime object (2012-11-30)
$lastDate  = $currentPeriod->getLastDate();  // DateTime object (2012-12-29)
```

Class `BaseSubscription` implements `Countable`, `ArrayAccess`, `Iterator` PHP interfaces, so you can easly count, access
and loop over each period. `BaseSubscriptionPeriod` inherits from PHP `DatePeriod` object, allowing to loop over each
day of the period:
```php
// Get periods count
$numPeriods = count($subscription); // 4

// Get the previous period
$previusPeriod = $subscription[$numPeriods - 1]; // Period from 2012-10-31 to 2012-11-29

// Loop over each day of the previous period
foreach($previusPeriod as date)
{
    // ...
}

// Loop over each period
foreach($subscription as $period)
{
    // ...
}

// Find out a period for the given date (may return null)
$period = $subscription->getPeriod(new \DateTime('2012-11-25')); // Period form 2012-10-01 to 2012-10-30
```
