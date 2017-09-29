<?php

use PHPUnit\Framework\TestCase;
use Xiaker\Gourd\Container;
use Tests\Fixtures\Author;
use Tests\Fixtures\AuthorInterface;
use Tests\Fixtures\Book;
use Tests\Fixtures\Reader;
use Tests\Fixtures\ReaderInterface;

class ContainerTest extends TestCase
{
    public function testSetBinding()
    {
        $container = new Container();
        $container->set('cb', function () {
            return true;
        });

        $this->assertTrue($container->get('cb'));
    }

    public function testSingletonBinding()
    {
        $container = new Container();
        $container->singleton('cb', function () {
            return true;
        });

        $this->assertTrue($container->get('cb'));
    }

    public function testMakeCallback()
    {
        $container = new Container();
        $container->singleton('cb', function () {
            return new Author();
        });

        $this->assertEquals(new Author(), $container->get('cb'));
    }

    public function testMakeDependencies()
    {
        $container = new Container();
        $container->singleton(AuthorInterface::class, Author::class);
        $container->singleton(ReaderInterface::class, Reader::class);
        $container->singleton('book', Book::class);

        $this->assertEquals('fatrbaby', $container->get('book')->getName());
    }

    public function testMakeObjectToUse()
    {
        $container = new Container();
        $container->set('reader', Reader::class, true);

        $reader = $container->get('reader');
        $this->assertEquals(true, $reader->isLike());
    }

    public function testDefaultValueCallback()
    {
        $container = new Container();
        $container->singleton('age', function ($age = 17) {
            return $age + 1;
        });

        $this->assertEquals(18, $container->get('age'));
    }

    public function testArraySet()
    {
        $container = new Container();
        $container['author'] = Author::class;

        $this->assertEquals('fatrbaby', $container->get('author')->getName());
    }

    public function testArrayGet()
    {
        $container = new Container();
        $container->singleton('cb', function () {
            return true;
        });

        $this->assertTrue($container['cb']);
    }

    public function testArrayIsset()
    {
        $container = new Container();
        $container->singleton('std', new stdClass());

        $this->assertTrue(isset($container['std']));
    }
}
