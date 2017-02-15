<?php

namespace Xiaker\Tests;

class Logger
{
    public function __construct(Handler $handler)
    {
        $handler->write();
    }
}
