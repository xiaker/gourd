<?php

namespace Xiaker\Gourd;

use \Exception;
use \ReflectionClass;
use \ReflectionFunction;
use \ReflectionParameter;
use \SplObjectStorage;

class Container implements ContainerInterface, \ArrayAccess
{
    protected $singletons = [];
    protected $storage = [];
    protected $factories = [];
    protected $reflections = [];

    public function __construct()
    {
        $this->factories = new SplObjectStorage();
    }

    public function make($name)
    {
        $raw = $this->fetch($name);

        if ($raw instanceof \Closure) {
            return $this->call($raw);
        }

        return $this->build($raw);
    }

    public function set($name, $concrete)
    {
        $this->storage[$name] = $concrete;
    }

    public function singleton($name, $concrete)
    {
        if (isset($this->singletons[$name])) {
            throw new Exception('Can not rewrite singleton Object.');
        }

        return $this->singletons[$name] = $concrete;
    }

    protected function fetch($name)
    {
        if (isset($this->singletons[$name])) {
            return $this->singletons[$name];
        }

        if (isset($this->storage[$name])) {
            return $this->storage[$name];
        }

        throw new \TypeError('make a wrong type');
    }

    protected function call($callable)
    {
        $reflection = new ReflectionFunction($callable);
        $parameters = $reflection->getParameters();
        $args = $this->getArguments($parameters);

        return $reflection->invokeArgs($args);
    }

    protected function build($class)
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();
        $arguments = $this->getArguments($parameters);

        return $reflection->newInstanceArgs($arguments);
    }

    protected function getArguments(ReflectionParameter $parameters)
    {
        $arguments = [];

        foreach ($parameters as $parameter) {
            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
            } elseif ($class = $parameter->getClass()->getName()) {
                $arguments[] = $this->make($class);
            } else {
                throw new Exception();
            }
        }

        return $arguments;
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
