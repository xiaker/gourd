<?php

namespace Xiaker\Gourd;

interface ContainerInterface
{
    public function make($name);

    public function instance($name, $instance);

    public function singleton($name, $concrete);
}
