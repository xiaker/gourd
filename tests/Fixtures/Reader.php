<?php

namespace Tests\Fixtures;

class Reader implements ReaderInterface
{
    public function read($text)
    {
        return $text;
    }

    public function isLike()
    {
        return true;
    }
}
