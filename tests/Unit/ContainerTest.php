<?php

use Tests\Fixtures\Author;
use Tests\Fixtures\AuthorInterface;
use Tests\Fixtures\Book;
use Tests\Fixtures\Bookshelf;
use Tests\Fixtures\Reader;
use Tests\Fixtures\ReaderInterface;
use Xiaker\Gourd\Container;
use Xiaker\Gourd\Exception\NotFoundException;

test('use set method', function () {
    $container = new Container;
    $container->set('book', 1);

    expect($container->has('book'))->toBeTrue();
});

test('use get method', function () {
    $container = new Container;
    $container->set('num', 1);

    expect($container->get('num'))->toBeInt()->toBe(1);
});

test('set instance by set method', function () {
    $instance = new Author;

    $container = new Container;
    $container->set('author', $instance);

    expect($container->get('author'))->toBe($instance);
});

test('set closure by set method', function () {
    $container = new Container;
    $container->set('donkey', function () {
        return 'awesome';
    });

    expect($container->get('donkey'))->toBeString()->toBe('awesome');
});

test('throw not found exception', function () {
    $container = new Container;
    $container->get('not-found');
})->throws(NotFoundException::class);

test('resolve closure default value', function () {
    $container = new Container;

    $container->set('sum', function ($a = 2, $b = 5) {
        return $a + $b;
    });

    expect($container->get('sum'))->toBeInt()->toEqual(7);
});

test('resolve class constructor default value', function () {
    $container = new Container;
    $container->set('shelf', Bookshelf::class);
    $container->set(ReaderInterface::class, Reader::class);

    $shelf = $container->get('shelf');

    expect($shelf)->toBeInstanceOf(Bookshelf::class)
        ->and($shelf->getReader())->toBeInstanceOf(ReaderInterface::class)
        ->and($shelf->getNumber())->toBeInt()->toEqual(5);
});

test('resolve dependency', function () {
    $container = new Container;
    $container->set(AuthorInterface::class, Author::class);
    $container->set(ReaderInterface::class, Reader::class);
    $container->set('book', Book::class);

    $book = $container->get('book');

    expect($book)
        ->toBeInstanceOf(Book::class)
        ->and($book->getReader())->toBeInstanceOf(ReaderInterface::class)
        ->and($book->getAuthor())->toBeInstanceOf(AuthorInterface::class)
        ->and($book->getName())->toBe('fatrbaby');
});

test('set by array access', function () {
    $container = new Container;
    $container['book'] = Book::class;
    $container[ReaderInterface::class] = Reader::class;
    $container[AuthorInterface::class] = Author::class;

    $book = $container['book'];

    expect($book)
        ->toBeInstanceOf(Book::class)
        ->and($book->getReader())->toBeInstanceOf(ReaderInterface::class)
        ->and($book->getAuthor())->toBeInstanceOf(AuthorInterface::class)
        ->and($book->getName())->toBe('fatrbaby');
});

test('get by array access', function () {
    $container = new Container;
    $container['author'] = function () {
        return new Author;
    };

    /* @var Author $author */
    $author = $container['author'];
    expect($author)->toBeInstanceOf(Author::class)
        ->and($author->getName())->toBeString()->toBe('fatrbaby');
});

test('throw not found exception from array access', function () {
    $container = new Container;
    $container['not-found'];
})->throws(NotFoundException::class);
