<?php

class Container implements \ArrayAccess
{
    protected $factories;

    public function __construct()
    {
        $this->factories = new \SplObjectStrorage();
    }
}
