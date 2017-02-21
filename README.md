##Gourd

PHP IoC container

###installation
`composer require xiaker/gourd`

###usage

```php

$container = new Xiaker\Gourd;

$container->set(User::class, function () {
    return new User();
});

$container->singleton('logger', Logger::class);

$container->make(User::class);
$container->make('logger');

```