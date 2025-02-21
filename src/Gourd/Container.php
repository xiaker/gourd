<?php

declare(strict_types=1);

namespace Xiaker\Gourd;

use ArrayAccess;
use Closure;
use Countable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionParameter;
use Xiaker\Gourd\Exception\ContainerException;
use Xiaker\Gourd\Exception\NotFoundException;

use function count;
use function is_object;

class Container implements ArrayAccess, ContainerInterface, Countable
{
    protected array $bindings = [];

    protected array $instances = [];

    /**
     * @return mixed|object|null
     *
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function get(string $id): mixed
    {
        if (! $this->has($id)) {
            throw new NotFoundException(sprintf('Binding "%s" does not exist.', $id));
        }

        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        $binding = $this->bindings[$id];

        if ($binding instanceof Closure) {
            $instance = $this->call($binding);

            return $this->cacheInstance($id, $instance);
        }

        if (is_object($binding)) {
            return $this->cacheInstance($id, $binding);
        }

        if (is_string($binding) && class_exists($binding)) {
            $built = $this->build($binding);

            return $this->cacheInstance($id, $built);
        }

        return $binding;
    }

    public function has(string $id): bool
    {
        return isset($this->bindings[$id]);
    }

    public function set(string $id, mixed $binding): bool
    {
        $this->bindings[$id] = $binding;

        return true;
    }

    /**
     * @return $this
     */
    public function register(ServiceProviderInterface $provider): Container
    {
        $provider->register($this);

        return $this;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     */
    public function call(Closure $id)
    {
        $reflection = new ReflectionFunction($id);

        if ($reflection->getNumberOfRequiredParameters() === 0) {
            return $reflection->invoke();
        }

        return $reflection->invokeArgs(
            $this->buildParameterValues($reflection->getParameters())
        );
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     */
    public function build($class): ?object
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $parameters = $constructor->getParameters();

        return $reflection->newInstanceArgs(
            $this->buildParameterValues($parameters)
        );
    }

    /**
     * @param  array<ReflectionParameter>  $parameters
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|ReflectionException
     * @throws ContainerException
     */
    protected function buildParameterValues(array $parameters): array
    {
        if (empty($parameters)) {
            return [];
        }

        $arguments = [];

        foreach ($parameters as $parameter) {
            if ($parameter->hasType() && $this->has($name = $parameter->getType()->getName())) {
                $arguments[] = $this->get($name);
            } else {
                $arguments[] = $this->tryGetValue($parameter);
            }
        }

        return $arguments;
    }

    /**
     * @throws ContainerException
     */
    protected function tryGetValue(ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new ContainerException('Unable to resolve value for parameter '.$parameter->getName());
    }

    protected function cacheInstance(string $id, mixed $instance): mixed
    {
        return $this->instances[$id] = $instance;
    }

    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        if ($this->has($offset)) {
            unset($this->bindings[$offset]);

            if (isset($this->instances[$offset])) {
                unset($this->instances[$offset]);
            }
        }
    }

    public function count(): int
    {
        return count($this->bindings);
    }
}
