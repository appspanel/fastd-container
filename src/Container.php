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
     * @var array
     */
    protected array $services = [];

    /**
     * @var array
     */
    protected array $map = [];

    /**
     * 实例数组
     *
     * @var array<string, mixed>
     */
    protected array $instances = [];

    /**
     * @param string $id
     * @param mixed $service
     * @return Container
     */
    public function add(string $id, mixed $service): Container
    {
        if (!($service instanceof Closure)) {
            if (is_object($service)) {
                $this->map[get_class($service)] = $id;
                $this->instances[$id] = $service;
            } elseif (is_string($service)) {
                $this->map[$service] = $id;
            }
        }

        $this->services[$id] = $service;

        return $this;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        if (isset($this->map[$id])) {
            $id = $this->map[$id];
        }

        return isset($this->services[$id]);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id): mixed
    {
        $id = $this->map[$id] ?? $id;

        if (!isset($this->services[$id])) {
            throw new NotFoundException($id);
        }

        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        $service  = $this->services[$id];

        if (is_string($service)) {
            $service = new $service;
        }

        $this->instances[$id] = $service;

        return $service;
    }


    /**
     * @param ServiceProviderInterface $provider
     */
    public function register(ServiceProviderInterface $provider): void
    {
        $provider->register($this);
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
    public function offsetGet(mixed $offset): object
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
    public function key(): mixed
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
