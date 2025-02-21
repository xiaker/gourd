<?php

declare(strict_types=1);

namespace Tests\Fixtures;

readonly class Bookshelf
{
    public function __construct(
        private ReaderInterface $reader,
        private int $number = 5,
    ) {}

    public function getReader(): ReaderInterface
    {
        return $this->reader;
    }

    public function getNumber(): int
    {
        return $this->number;
    }
}
