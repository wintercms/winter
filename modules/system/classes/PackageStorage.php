<?php

namespace System\Classes;

use Composer\Semver\VersionParser;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Winter\Packager\Storage\Storage;
use Winter\Storm\Support\Facades\Config;

class PackageStorage implements Storage
{
    public function get(string $namespace, string $name, ?string $version = null): ?array
    {
        if (Config::get('marketplace.cache') === false) {
            return null;
        }

        $data = $this->getCache()->get($this->getCacheKey($namespace, $name));

        if (!is_null($version)) {
            $version = $this->normalizeVersion($version);

            if (!isset($data['versions'][$version])) {
                return null;
            }

            return $data['versions'][$version];
        }

        return $data;
    }

    public function has(string $namespace, string $name, ?string $version = null): bool
    {
        if (Config::get('marketplace.cache') === false) {
            return null;
        }

        if (is_null($version)) {
            $this->getCache()->get($this->getCacheKey($namespace, $name));
        }

        return $this->get($namespace, $name, $version) !== null;
    }

    public function set(string $namespace, string $name, string $version, array $packageData): void
    {
        if (Config::get('marketplace.cache') === false) {
            return;
        }

        $data = $this->getCache()->get($this->getCacheKey($namespace, $name));
        if (!isset($data['versions'])) {
            $data['versions'] = [];
        }

        $version = $this->normalizeVersion($version);
        $data['versions'][$version] = $packageData;

        $this->getCache()->put($this->getCacheKey($namespace, $name), $data);
    }

    public function forget(string $namespace, string $name, ?string $version = null): void
    {
        if (Config::get('marketplace.cache') === false) {
            return;
        }

        $this->getCache()->forget($this->getCacheKey($namespace, $name));
    }

    public function clear(): void
    {
        if (Config::get('marketplace.cache') === false) {
            return;
        }

        $this->getCache()->clear();
    }

    protected function getCache(): Repository
    {
        return Cache::store(Config::get('marketplace.store'));
    }

    protected function getCacheKey(string $namespace, string $name): string
    {
        return 'marketplace.' . $namespace . '.' . $name;
    }

    protected function normalizeVersion(string $version): string
    {
        $parser = new VersionParser;
        return $parser->normalize($version);
    }
}
