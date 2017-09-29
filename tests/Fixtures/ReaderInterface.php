<?php

namespace Tests\Fixtures;

interface ReaderInterface
{
    public function read($text);

    public function isLike();
}
