<?php

namespace Xiaker\Gourd;

use \InvalidArgumentException;
use \OutOfBoundsException;
use \LogicException;
use \ReflectionClass;
use \ReflectionFunction;
use \SplObjectStorage;

class Container implements ContainerInterface, \ArrayAccess
{
    protected $singletons = [];
    protected $storage = [];
    protected $factories = [];
    protected $bindings = [];

    public function __construct()
    {
        $this->factories = new SplObjectStorage();
    }

    public function make($name)
    {
        if (isset($this->bindings[$name])) {
            return $this->bindings[$name];
        }

        $raw = $this->raw($name);

        if ($raw instanceof \Closure) {
            return $this->call($raw);
        }

        if (is_object($raw)) {
            return $raw;
        }

        $this->bindings[$name] = $this->build($raw);

        return $this->bindings[$name];
    }

    public function set($name, $concrete)
    {
        $this->storage[$name] = $concrete;
    }

    public function singleton($name, $concrete)
    {
        if (isset($this->storage[$name])) {
            throw new LogicException('Binding already exists.');
        }

        if (isset($this->singletons[$name])) {
            throw new LogicException('Cannot override singleton binding.');
        }

        $this->singletons[$name] = $concrete;
    }

    protected function raw($name)
    {
        if (isset($this->singletons[$name])) {
            return $this->singletons[$name];
        }

        if (isset($this->storage[$name])) {
            return $this->storage[$name];
        }

        throw new OutOfBoundsException('Your make instance does not contain.');
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

        if (null === $constructor) {
            return $reflection->newInstance();
        }

        $parameters = $constructor->getParameters();
        $arguments = $this->getArguments($parameters);

        return $reflection->newInstanceArgs($arguments);
    }

    protected function getArguments($parameters)
    {
        $arguments = [];

        foreach ($parameters as $parameter) {
            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
            } elseif ($class = $parameter->getClass()) {
                $arguments[] = $this->make($class->getName());
            } else {
                throw new InvalidArgumentException(sprintf('Unable to resolve parameter: $%s', $parameter->getName()));
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
        if (isset($this->storage[$offset])) {
            return $this->storage[$offset];
        }

        throw new OutOfBoundsException('Undefined offset of Container.');
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
