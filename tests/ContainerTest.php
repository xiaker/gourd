<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Author;
use Tests\Fixtures\AuthorInterface;
use Tests\Fixtures\Book;
use Tests\Fixtures\DemoServiceProvider;
use Tests\Fixtures\Reader;
use Tests\Fixtures\ReaderInterface;
use Xiaker\Gourd\Container;

class ContainerTest extends TestCase
{
    public function testBindingBySetMethod()
    {
        $container = new Container();
        $container->set('cb', function () {
            return true;
        });

        $this->assertTrue($container->get('cb'));
    }

    public function testBindingByClosure()
    {
        $container = new Container();
        $container->set('cb', function () {
            return new Author();
        });

        $this->assertEquals(new Author(), $container->get('cb'));
    }

    public function testDependenciesResolution()
    {
        $container = new Container();
        $container->set(AuthorInterface::class, Author::class);
        $container->set(ReaderInterface::class, Reader::class);
        $container->set('book', Book::class);

        $this->assertEquals('fatrbaby', $container->get('book')->getName());
    }

    public function testGetAndUseObject()
    {
        $container = new Container();
        $container->set('reader', Reader::class);

        $reader = $container->get('reader');
        $this->assertTrue($reader->isLike());
    }

    public function testDefaultValueCallback()
    {
        $container = new Container();
        $container->set('age', function ($age = 17) {
            return $age + 1;
        });

        $this->assertEquals(18, $container->get('age'));
    }

    public function testBindingByArrayOperation()
    {
        $container = new Container();
        $container['author'] = Author::class;

        $this->assertEquals('fatrbaby', $container->get('author')->getName());
    }

    public function testGetObjectByArrayOperation()
    {
        $container = new Container();
        $container->set('cb', function () {
            return true;
        });

        $this->assertTrue($container['cb']);
    }

    public function testArrayIsset()
    {
        $container = new Container();
        $container->set('std', new \stdClass());

        $this->assertTrue(isset($container['std']));
    }

    public function testServiceProvider()
    {
        $container = new Container();
        $container->register(new DemoServiceProvider());

        $this->assertEquals('FromDemoServiceProvider', $container->get('demo'));
    }
}
