# Enqueue Mongodb message queue transport

Allows to use [MongoDB](https://www.mongodb.com/) as a message queue broker. 

* [Installation](#installation)
* [Create context](#create-context)
* [Send message to topic](#send-message-to-topic)
* [Send message to queue](#send-message-to-queue)
* [Send priority message](#send-priority-message)
* [Send expiration message](#send-expiration-message)
* [Send delayed message](#send-delayed-message)
* [Consume message](#consume-message)
* [Subscription consumer](#subscription-consumer)

## Installation

```bash
$ composer require enqueue/mongodb
```

## Create context

```php
<?php
use Enqueue\Mongodb\MongodbConnectionFactory;

// connects to localhost
$connectionFactory = new MongodbConnectionFactory();

// same as above
$factory = new MongodbConnectionFactory('mongodb:');

// same as above
$factory = new MongodbConnectionFactory([]);

$factory = new MongodbConnectionFactory([
    'dsn' => 'mongodb://localhost:27017/db_name',
    'dbname' => 'enqueue',
    'collection_name' => 'enqueue',
    'polling_interval' => '1000',
]);

$psrContext = $factory->createContext();

// if you have enqueue/enqueue library installed you can use a factory to build context from DSN 
$psrContext = (new \Enqueue\ConnectionFactoryFactory())->create('mongodb:')->createContext();
```

## Send message to topic 

```php
<?php
/** @var \Enqueue\Mongodb\MongodbContext $psrContext */
/** @var \Enqueue\Mongodb\MongodbDestination $fooTopic */

$message = $psrContext->createMessage('Hello world!');

$psrContext->createProducer()->send($fooTopic, $message);
```

## Send message to queue 

```php
<?php
/** @var \Enqueue\Mongodb\MongodbContext $psrContext */
/** @var \Enqueue\Mongodb\MongodbDestination $fooQueue */

$message = $psrContext->createMessage('Hello world!');

$psrContext->createProducer()->send($fooQueue, $message);
```

## Send priority message

```php
<?php
/** @var \Enqueue\Mongodb\MongodbContext $psrContext */

$fooQueue = $psrContext->createQueue('foo');

$message = $psrContext->createMessage('Hello world!');

$psrContext->createProducer()
    ->setPriority(5) // the higher priority the sooner a message gets to a consumer
    //    
    ->send($fooQueue, $message)
;
```

## Send expiration message

```php
<?php
/** @var \Enqueue\Mongodb\MongodbContext $psrContext */
/** @var \Enqueue\Mongodb\MongodbDestination $fooQueue */

$message = $psrContext->createMessage('Hello world!');

$psrContext->createProducer()
    ->setTimeToLive(60000) // 60 sec
    //    
    ->send($fooQueue, $message)
;
```

## Send delayed message

```php
<?php
use Enqueue\AmqpTools\RabbitMqDlxDelayStrategy;

/** @var \Enqueue\Mongodb\MongodbContext $psrContext */
/** @var \Enqueue\Mongodb\MongodbDestination $fooQueue */

// make sure you run "composer require enqueue/amqp-tools".

$message = $psrContext->createMessage('Hello world!');

$psrContext->createProducer()
    ->setDeliveryDelay(5000) // 5 sec
    
    ->send($fooQueue, $message)
;
````   

## Consume message:

```php
<?php
/** @var \Enqueue\Mongodb\MongodbContext $psrContext */
/** @var \Enqueue\Mongodb\MongodbDestination $fooQueue */

$consumer = $psrContext->createConsumer($fooQueue);

$message = $consumer->receive();

// process a message

$consumer->acknowledge($message);
// $consumer->reject($message);
```

## Subscription consumer

```php
<?php
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrConsumer;

/** @var \Enqueue\Mongodb\MongodbContext $psrContext */
/** @var \Enqueue\Mongodb\MongodbDestination $fooQueue */
/** @var \Enqueue\Mongodb\MongodbDestination $barQueue */

$fooConsumer = $psrContext->createConsumer($fooQueue);
$barConsumer = $psrContext->createConsumer($barQueue);

$subscriptionConsumer = $psrContext->createSubscriptionConsumer();
$subscriptionConsumer->subscribe($fooConsumer, function(PsrMessage $message, PsrConsumer $consumer) {
    // process message
    
    $consumer->acknowledge($message);
    
    return true;
});
$subscriptionConsumer->subscribe($barConsumer, function(PsrMessage $message, PsrConsumer $consumer) {
    // process message
    
    $consumer->acknowledge($message);
    
    return true;
});

$subscriptionConsumer->consume(2000); // 2 sec
```

[back to index](../index.md)
