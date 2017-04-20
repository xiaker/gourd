<?php

use PHPUnit\Framework\TestCase;
use Xiaker\Gourd\Container;
use Tests\Logger;
use Tests\Handler;

class ContainerTest extends TestCase
{
    public function testSetBinding()
    {
        $container = new Container();
        $instance = new stdClass();

        $container->set('stdClass', $instance);

        $this->assertAttributeEquals(['stdClass' => $instance], 'storage', $container);
    }

    public function testSingletonBinding()
    {
        $container = new Container();
        $instance = new stdClass();

        $container->singleton('stdClass', $instance);

        $this->assertAttributeEquals(['stdClass' => $instance], 'singletons', $container);
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

    public function testMakeDependencies()
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

    public function testDefaultValueCallback()
    {
        $container = new Container();
        $container->set('cb', function ($name = 'fatrbaby') {
            echo $name, PHP_EOL;
            return 'hello ' . $name;
        });

        $this->assertEquals('hello fatrbaby', $container->make('cb'));
    }

    public function testArraySet()
    {
        $container = new Container();
        $container['handler'] = new Handler();

        $this->assertEquals('fatrbaby', $container->make('handler')->author());
    }

    public function testArrayGet()
    {
        $container = new Container();
        $container->set('handler', new Handler());

        $this->assertEquals('fatrbaby', $container['handler']->author());
    }

    public function testArrayIsset()
    {
        $container = new Container();
        $container->set('handler', new Handler());
        $this->assertTrue(isset($container['handler']));
    }

    /**
     * @expectedException \LogicException
     */
    public function testOverrideSingletonBinding()
    {
        $container = new Container();
        $container->singleton('handler', new Handler());
        $container->singleton('handler', new stdClass());
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testArrayUnset()
    {
        $container = new Container();
        $container->set('handler', new Handler());
        unset($container['handler']);
        $container->make('handler');
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testMakeError()
    {
        $container = new Container();
        $container->make('nonexistent');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgumentBinding()
    {
        $container = new Container();
        $container->set('who', function ($num) {
            return $num + 1;
        });

        $who = $container->make('who');

        var_dump($who);
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testUndefinedIndex()
    {
        $container = new Container();
        $binding = $container['undefined'];
    }
}
