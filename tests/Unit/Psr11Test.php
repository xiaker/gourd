<?php

declare(strict_types=1);

use Xiaker\Gourd\Container;
use Xiaker\Gourd\Exception\ContainerException;
use Xiaker\Gourd\Exception\NotFoundException;

test('has get and set method', function (): void {
    expect(Container::class)->toHaveMethods(['get', 'set']);
});

test('throws not found exception', function (): void {
    $container = new Container;
    $container->get('foo');
})->throws(NotFoundException::class);

test('throws container exception', function (): void {
    $container = new Container;
    $container->set('sum', function ($a): int {
        return $a + 1;
    });

    $container->get('sum');
})->throws(ContainerException::class);