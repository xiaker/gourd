<?php

use Xiaker\Gourd\Container;
use PHPUnit\Framework\TestCase;
use Xiaker\Tests\Logger;
use Xiaker\Tests\Handler;

class ContainerTest extends TestCase
{
    public function testSetObject()
    {
        $container = new Container();
        $instance = new stdClass();

        $container->set('stdClass', $instance);

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

    public function testDependenciesMake()
    {
        $container = new Container();
        $container->set(Logger::class, Logger::class);
        $container->singleton(Handler::class, Handler::class);
        $handler = $container->make(Handler::class);
        $logger = $container->make(Logger::class);

        $this->assertEquals(new Logger($handler), $logger);
    }

    public function testMakeObjectToUse()
    {
        $container = new Container();
        $handler = new Handler();
        $container->set('handler', $handler);

        $this->assertEquals('fatrbaby', $container->make('handler')->author());
    }
}
