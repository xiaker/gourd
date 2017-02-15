<?php

namespace Xiaker\Gourd;

interface ContainerInterface
{
    public function make($name);

    public function set($name, $concrete);

    public function singleton($name, $concrete);
}
