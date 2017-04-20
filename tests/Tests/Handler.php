<?php

namespace Tests;

class Handler
{
    public function write()
    {
        echo 'write logs...', PHP_EOL;
    }

    public function author()
    {
        return 'fatrbaby';
    }
}
