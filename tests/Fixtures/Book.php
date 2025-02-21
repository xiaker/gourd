<?php

namespace Tests\Fixtures;

class Book
{
    protected AuthorInterface $author;

    protected ReaderInterface $reader;

    public function __construct(AuthorInterface $author, ReaderInterface $reader)
    {
        $this->author = $author;
        $this->reader = $reader;
    }

    public function getName(): string
    {
        return 'fatrbaby';
    }

    public function getAuthor(): AuthorInterface
    {
        return $this->author;
    }

    public function getReader(): ReaderInterface
    {
        return $this->reader;
    }
}
