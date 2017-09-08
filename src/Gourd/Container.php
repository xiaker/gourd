<?php

namespace Xiaker\Gourd;

use ArrayAccess;
use Closure;
use Countable;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionFunction;

class Container implements ContainerInterface, ArrayAccess, Countable
{
    protected $bindings = [];
    protected $singletons = [];
    protected $instances = [];

    public function get($id)
    {
        $id = $this->normalizeId($id);

        if (!$this->has($id)) {
            throw new NotFoundException();
        }

        if (
            isset($this->singletons[$id]) &&
            $this->singletons[$id] &&
            isset($this->instances[$id])
        ) {
            return $this->instances[$id];
        }

        $binding = $this->bindings[$id];

        if ($binding instanceof Closure) {
            return $this->call($binding);
        }

        if (is_object($binding)) {
            return $binding;
        }

        return $this->build($binding);
    }

    public function has($id)
    {
        $id = $this->normalizeId($id);

        return isset($this->bindings[$id]);
    }

    public function set($id, $binding, $singleton = true)
    {
        if ($singleton) {
            return $this->singleton($id, $binding);
        }

        $id = $this->normalizeId($id);
        $this->singletons[$id] = false;
        $this->bindings[$id] = $binding;

        return true;
    }

    public function singleton($id, $binding)
    {
        $id = $this->normalizeId($id);
        $this->singletons[$id] = true;
        $this->bindings[$id] = $binding;

        return true;
    }

    public function call(Closure $id)
    {
        $reflection = new ReflectionFunction($id);
        $parameters = $reflection->getParameters();
        $args = $this->buildParameterValues($parameters);

        return $reflection->invokeArgs($args);
    }

    public function build($class)
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if (null === $constructor) {
            return $reflection->newInstance();
        }

        $parameters = $constructor->getParameters();
        $arguments = $this->buildParameterValues($parameters);

        return $reflection->newInstanceArgs($arguments);
    }

    protected function buildParameterValues(array $parameters)
    {
        $arguments = [];

        foreach ($parameters as $parameter) {
            if ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
            } elseif ($class = $parameter->getClass()) {
                $arguments[] = $this->get($class->getName());
            } else {
                throw new ContainerException(sprintf('Unable to resolve parameter: $%s', $parameter->getName()));
            }
        }

        return $arguments;
    }

    protected function normalizeId($id)
    {
        if (is_scalar($id)) {
            return $id;
        }

        return json_encode($id);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->singleton($offset, $value);
    }

    public function offsetUnset($offset)
    {
        if ($this->has($offset)) {
            unset($this->bindings[$offset]);
        }
    }

    public function count()
    {
        return count($this->bindings);
    }
}
