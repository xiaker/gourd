<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Xiaker\Gourd\Container;
use Xiaker\Gourd\ServiceProviderInterface;

class TestingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $gourd): void
    {
        $gourd->set(AuthorInterface::class, Author::class);
        $gourd->set(ReaderInterface::class, Reader::class);
        $gourd->set('book', Book::class);
    }
}
