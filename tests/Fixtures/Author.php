<?php

namespace Tests\Fixtures;

class Author implements AuthorInterface
{
    public function getName(): string
    {
        return 'fatrbaby';
    }
}
