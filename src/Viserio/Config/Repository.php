<?php
declare(strict_types=1);
namespace Viserio\Config;

use ArrayIterator;
use IteratorAggregate;
use Narrowspark\Arr\Arr;
use Viserio\Contracts\Config\Repository as RepositoryContract;
use Viserio\Contracts\Parsers\Traits\LoaderAwareTrait;

class Repository implements RepositoryContract, IteratorAggregate
{
    use LoaderAwareTrait;

    /**
     * Config folder path.
     *
     * @var string
     */
    protected $path;

    /**
     * Cache of previously parsed keys.
     *
     * @var array
     */
    protected $keys = [];

    /**
     * Storage array of values.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Import configuation from file.
     * Can be grouped together.
     *
     * @param string      $file
     * @param string|null $group
     *
     * @return $this
     */
    public function import(string $file, string $group = null): RepositoryContract
    {
        $config = $this->getLoader()->load($file, $group);

        $this->setArray($config);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value): RepositoryContract
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $default = null)
    {
        if (! $this->offsetExists($key)) {
            return $default;
        }

        return $this->offsetGet($key);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $key): RepositoryContract
    {
        return $this->offsetUnset($key);
    }

    /**
     * {@inheritdoc}
     */
    public function setArray(array $values = []): RepositoryContract
    {
        $this->data = Arr::merge($this->data, $values);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function allFlat(): array
    {
        return Arr::flatten($this->data, '.');
    }

    /**
     * {@inheritdoc}
     */
    public function getKeys(): array
    {
        return array_keys(Arr::flatten($this->data, '.'));
    }

    /**
     * Get a value from a nested array based on a separated key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return Arr::get($this->data, $key);
    }

    /**
     * Set nested array values based on a separated key.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function offsetSet($key, $value): RepositoryContract
    {
        $this->data = Arr::set($this->data, $key, $value);

        return $this;
    }

    /**
     * Check an array has a value based on a separated key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return Arr::has($this->data, $key);
    }

    /**
     * Remove nested array value based on a separated key.
     *
     * @param string $key
     *
     * @return $this
     */
    public function offsetUnset($key): RepositoryContract
    {
        Arr::forget($this->data, $key);

        return $this;
    }

    /**
     * Get an ArrayIterator for the stored items.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->all());
    }
}
