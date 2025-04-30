<?php

namespace System\Classes\Asset;

class AssetContainer implements \ArrayAccess
{
    protected array $assets = ['js' => [], 'css' => [], 'rss' => [], 'vite' => []];

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->assets[$offset]);
    }

    public function &offsetGet(mixed $offset): mixed
    {
        return $this->assets[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->assets[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->assets[$offset]);
    }
}
