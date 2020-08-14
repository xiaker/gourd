<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Xiaker\Gourd\Container;

class Psr11Test extends TestCase
{
    public function testGetMethod()
    {
        $container = new Container();

        $this->assertTrue(method_exists($container, 'get'));
    }

    public function testHasMethod()
    {
        $container = new Container();

        $this->assertTrue(method_exists($container, 'has'));
    }

    public function testGotSameObjectInTwiceGet()
    {
        $container = new Container();

        $container['foo'] = function () {
            return new \SplStack();
        };

        $first = $container->get('foo');
        $second = $container->get('foo');

        $this->assertEquals($second, $first);
    }

    public function testThrowNotFountExceptionWhenBindingNotFound()
    {
        $this->expectException(NotFoundExceptionInterface::class);
        $container = new Container();
        $container->get('foo');
    }

    public function testThrowContainerExceptionWhenOtherError()
    {
        $this->expectException(ContainerExceptionInterface::class);
        $container = new Container();
        $container->set([], null);
    }
}
