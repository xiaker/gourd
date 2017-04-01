<?php

namespace Xiaker\Gourd;

use ArrayAccess;
use Countable;
use Closure;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface, ArrayAccess, Countable
{
    protected $bindings = [];

    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException();
        }

        $raw = $this->bindings[$id];

        if ($raw instanceof Closure) {
            return $this->call($raw);
        }

        return $this->make($raw);
    }

    public function has($id)
    {
        return $this->offsetExists($id);
    }

    public function call(Closure $id)
    {
        // todo
    }

    public function make($id, $singleton = true)
    {
        // todo
    }

    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->bindings[$offset];
        }

        throw new NotFoundException();
    }

    public function offsetExists($offset)
    {
        return isset($this->bindings[$offset]);
    }

    public function offsetSet($offset, $value)
    {
        if ($this->offsetExists($offset)) {
            throw new ContainerException('Offset always ready exists.');
        }

        $this->bindings[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->bindings[$offset]);
        }
    }

    public function count()
    {
        return count($this->bindings);
    }
}
