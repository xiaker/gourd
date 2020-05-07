# Gourd

Lightweight PHP IoC container, follow [`PSR-11`](https://www.php-fig.org/psr/psr-11/)

## installation
`composer require xiaker/gourd`

## usage

```php
$container = new Xiaker\Gourd\Container;

$container->set(User::class, function () {
    return new User();
});

$container->singleton('logger', Logger::class);

$container->make(User::class);
$container->make('logger');
```

## more
see `test case`