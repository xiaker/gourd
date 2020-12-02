<?php

declare(strict_types=1);

namespace Xiaker\Gourd;

use ArrayAccess;
use Closure;
use Countable;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionFunction;
use Xiaker\Gourd\Exception\ContainerException;
use Xiaker\Gourd\Exception\NotFoundException;

class Container implements ContainerInterface, ArrayAccess, Countable
{
    protected $bindings = [];
    protected $instances = [];

    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException();
        }

        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        $binding = $this->bindings[$id];

        if ($binding instanceof Closure) {
            $instance = $this->call($binding);
            $this->cacheInstance($id, $instance);

            return $instance;
        }

        if (is_object($binding)) {
            $this->cacheInstance($id, $binding);
            return $binding;
        }

        $built = $this->build($binding);
        $this->cacheInstance($id, $built);

        return $built;
    }

    public function has($id): bool
    {
        $this->checkId($id);

        return isset($this->bindings[$id]);
    }

    public function set($id, $binding): bool
    {
        $this->checkId($id);
        $this->bindings[$id] = $binding;

        return true;
    }

    public function register(ServiceProviderInterface $provider)
    {
        $provider->register($this);

        return $this;
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

    protected function cacheInstance($id, $instance)
    {
        return $this->instances[$id] = $instance;
    }

    public function checkId($id)
    {
        if (!is_scalar($id)) {
            throw new ContainerException('Invalid Container id');
        }
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
        return $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        if ($this->has($offset)) {
            unset($this->bindings[$offset]);

            if (isset($this->instances[$offset])) {
                unset($this->instances[$offset]);
            }
        }
    }

    public function count()
    {
        return count($this->bindings);
    }
}
