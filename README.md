# Gourd

Lightweight PHP IoC container, follow [`PSR-11`](https://www.php-fig.org/psr/psr-11/)

## features
 - Auto-wiring
 - Dependency resolution
 - Service Provider

## installation
`composer require xiaker/gourd`

## usage

```php
$container = new Xiaker\Gourd\Container;

$container->set(User::class, function () {
    return new User();
});

$container->set('logger', Logger::class);

$user = $container->get(User::class);
...

$logger = $container->get('logger');
...

$logger2 = $container['logger'];
...
```

## more
see `test case`