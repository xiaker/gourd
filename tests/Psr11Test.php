<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
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

    public function testGotSameObjectGetTwice()
    {
        $container = new Container();

        $container['foo'] = function () {
            return new \SplStack();
        };

        $first = $container->get('foo');
        $second = $container->get('foo');

        $this->assertEquals($second, $first);
    }
}
