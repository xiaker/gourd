<?php

namespace Xiaker\Gourd;

use \Exception;
use \ReflectionClass;
use \ReflectionFunction;
use \ReflectionParameter;


class Container implements \ArrayAccess
{
    protected $storage = [];
    protected $factories;

    public function __construct()
    {
        $this->factories = new \SplObjectStrorage();
    }

    public function make($name)
    {
        if (!isset($this->storage[$name])) {
            throw new Exception();
        }

        $raw = $this->storage[$name];

        if (is_object($raw)) {
            return $raw;
        }

        if ($raw instanceof \Closure) {
            $this->storage[$name] = $this->call($raw);
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

    public function call($callable)
    {
        $reflection = new ReflectionFunction($callable);
        $parameters = $reflection->getParameters();
        $args = $this->parseArgs($parameters);

        return $reflection->invokeArgs($args);
    }

    public function object($class)
    {
        $reflection = new ReflectionClass($class);
    }

    public function parseArgs(ReflectionParameter $parameter)
    {
        $args = [];

        foreach ($parameter as $param) {
            $class = $param->getClass();

            if (null === $class) {
                throw new \Exception();
            }

            $arg = $class->name;
            $args[] = $this->get($arg);
        }

        return $args;
    }
}
