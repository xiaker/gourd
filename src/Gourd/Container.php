<?php

namespace Xiaker\Gourd;

use \Exception;

class Container implements \ArrayAccess
{
    protected $storage = [];
    protected $factories;

    public function __construct()
    {
        $this->factories = new \SplObjectStrorage();
    }

    public function get($name)
    {
        if (!isset($this->storage[$name])) {
            throw new Exception();
        }

        $raw = $this->storage[$name];

        if ($raw instanceof \Closure) {
            return call_user_func($raw);
        }

        if (is_string($raw)) {
           $reflection = new \ReflectionClass($raw);
           return $reflection->newInstance();
        }
    }

    public function instance($name, $instance)
    {
        $this->storage[$name] = $instance;
    }

    public function singleton($name, $concrete)
    {
        if (isset($this->storage[$name])) {
            throw new Exception('Can not reload Object.');
        }

        return $this->storage[$name] = $concrete;
    }

    public function offsetExists($offset)
    {
        return isset($this->storage[$offset]);
    }

    public function offsetGet($offset)
    {
        if ($this->storage[$offset]) {
            return $this->storage[$offset];
        }

        throw new Exception('Undefined offset of Container.');
    }

    public function offsetSet($offset, $value)
    {
        $this->storage[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if (isset($this->storage[$offset])) {
            unset($this->storage[$offset]);
        }
    }
}
