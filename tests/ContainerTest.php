<?php

use Xiaker\Gourd\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testSetInstance()
    {
        $container = new Container();
        $instance = new stdClass();

        $container->instance('stdClass', $instance);

        $this->assertAttributeEquals(['stdClass' => $instance], 'storage', $container);
    }

    public function testMakeCallback()
    {
        $container = new Container();
        $instance = new stdClass();

        $container->singleton('cb', function () use ($instance) {
            return $instance;
        });

        $made = $container->make('cb');

        $this->assertEquals($instance, $made);
    }
}
