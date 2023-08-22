<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/fastdlabs
 * @link      https://www.fastdlabs.com/
 */

namespace FastD\Container;

use ArrayAccess;
use Closure;
use Iterator;
use Psr\Container\ContainerInterface;

/**
 * Class Container
 *
 * @package FastD\Container
 */
class Container implements ContainerInterface, ArrayAccess, Iterator
{
    /**
     * @var array<string,mixed>
     */
    protected array $services = [];

    /**
     * @var array<string,string>
     */
    protected array $map = [];

    /**
     * @var array<string, \FastD\Container\InjectionInterface>
     */
    protected array $injections = [];

    /**
     * Adds a service.
     *
     * @param string $id The service's id.
     * @param mixed $service The service or its definition.
     * @return \FastD\Container\Container This container.
     */
    public function add(string $id, mixed $service): static
    {
        if (!($service instanceof Closure)) {
            if (is_object($service)) {
                $this->map[get_class($service)] = $id;
            } elseif (is_string($service)) {
                $this->map[$service] = $id;
            }
        }

        $this->services[$id] = $service;

        return $this;
    }

    /**
     * Gets a service.
     *
     * @param string $id The service's id.
     * @return mixed The service.
     */
    public function get(string $id): mixed
    {
        $id = $this->map[$id] ?? $id;

        if (!isset($this->services[$id])) {
            throw new NotFoundException($id);
        }

        $service = $this->services[$id];

        if (is_object($service)) {
            // magic invoke class
            if (method_exists($service, 'bindTo') && is_callable($service)) {
                return $service($this);
            }

            // anonymous function
            if (is_callable($service)) {
                return $service;
            }
        }

        return $service;
    }

    /**
     * Tests whether a service exists.
     *
     * @param string $id The service's id.
     * @return bool `true` if it does, `false` otherwise.
     */
    public function has(string $id): bool
    {
        if (isset($this->map[$id])) {
            $id = $this->map[$id];
        }

        return isset($this->services[$id]);
    }

    /**
     * @param string $id The service's id.
     * @param array $arguments
     * @return mixed
     * @throws NotFoundException
     */
    public function make(string $id, array $arguments = []): mixed
    {
        if (!$this->has($id)) {
            throw new NotFoundException($id);
        }

        if (!isset($this->injections[$id])) {
            $service = $this->get($id);

            $this->injections[$id] = (new Injection($service))->withContainer($this);
        }

        return $this->injections[$id]->make($arguments);
    }

    /**
     * Registers a service provider.
     *
     * @param ServiceProviderInterface $serviceProvider The service provider.
     * @return \FastD\Container\Container This container.
     */
    public function register(ServiceProviderInterface $serviceProvider): static
    {
        $serviceProvider->register($this);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @since 5.0.0
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    /**
     * {@inheritDoc}
     * @since 5.0.0
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * {@inheritDoc}
     * @since 5.0.0
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->add($offset, $value);
    }

    /**
     * {@inheritDoc}
     * @since 5.0.0
     */
    public function offsetUnset(mixed $offset): void
    {
        if (isset($this->map[$offset])) {
            unset($this->map[$offset]);
        }

        if (isset($this->services[$offset])) {
            unset($this->services[$offset]);
        }
    }

    /**
     * {@inheritDoc}
     * @since 5.0.0
     */
    public function current(): mixed
    {
        return $this->get(key($this->services));
    }

    /**
     * {@inheritDoc}
     * @since 5.0.0
     */
    public function next(): void
    {
        next($this->services);
    }

    /**
     * {@inheritDoc}
     * @since 5.0.0
     */
    public function key(): string|int|null
    {
        return key($this->services);
    }

    /**
     * {@inheritDoc}
     * @since 5.0.0
     */
    public function valid(): bool
    {
        return isset($this->services[$this->key()]);
    }

    /**
     * {@inheritDoc}
     * @since 5.0.0
     */
    public function rewind(): void
    {
        reset($this->services);
    }
}
