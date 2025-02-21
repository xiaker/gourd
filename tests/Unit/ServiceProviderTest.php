<?php

declare(strict_types=1);

use Tests\Fixtures\AuthorInterface;
use Tests\Fixtures\Book;
use Tests\Fixtures\ReaderInterface;
use Tests\Fixtures\TestingServiceProvider;
use Xiaker\Gourd\Container;

test('service provider register', function () {
    $container = new Container;
    $provider = new TestingServiceProvider;
    $provider->register($container);

    $book = $container->get('book');

    expect($book)
        ->toBeInstanceOf(Book::class)
        ->and($book->getReader())->toBeInstanceOf(ReaderInterface::class)
        ->and($book->getAuthor())->toBeInstanceOf(AuthorInterface::class)
        ->and($book->getName())->toBe('fatrbaby');
});
