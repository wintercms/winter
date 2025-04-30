<?php

namespace System\Classes\Asset;

class AssetContainer extends \ArrayIterator
{
    protected array $assets = ['js' => [], 'css' => [], 'rss' => [], 'vite' => []];

    public function offsetExists(mixed $key): bool
    {
        return isset($this->assets[$key]);
    }

    public function &offsetGet(mixed $key): mixed
    {
        return $this->assets[$key];
    }

    public function offsetSet(mixed $key, mixed $value): void
    {
        $this->assets[$key] = $value;
    }

    public function offsetUnset(mixed $key): void
    {
        unset($this->assets[$key]);
    }

    public function current(): mixed
    {
        return current($this->assets);
    }

    public function next(): void
    {
        next($this->assets);
    }

    public function key(): mixed
    {
        return key($this->assets);
    }

    public function valid(): bool
    {
        return isset($this->assets[key($this->assets)]);
    }

    public function rewind(): void
    {
        reset($this->assets);
    }

    public function count(): int
    {
        return count($this->assets);
    }
}
