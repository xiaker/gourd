<?php

namespace Tests\Fixtures;

class Book
{
    protected $author;
    protected $reader;

    public function __construct(AuthorInterface $author, ReaderInterface $reader)
    {
        $this->reader = $reader;
        $this->author = $author;
    }

    public function getName()
    {
        return 'fatrbaby';
    }
}
