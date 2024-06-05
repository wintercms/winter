<?php

namespace System\Classes;

use InvalidArgumentException;
use RuntimeException;
use Winter\Storm\Support\Facades\File;

class PackageJson
{
    protected array $data;

    /**
     * Create a new instance with optional path, loads file if file already exists
     *
     * @param string|null $path
     */
    public function __construct(
        protected ?string $path = null
    ) {
        $this->data = File::exists($this->path)
            ? json_decode(File::get($this->path), JSON_OBJECT_AS_ARRAY)
            : [];
    }

    /**
     * Returns the package name if set
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    /**
     * Sets the package name, throws `InvalidArgumentException` on invalid name
     *
     * @param string|null $name
     * @return $this
     */
    public function setName(?string $name): static
    {
        if (is_null($name)) {
            unset($this->data['name']);
            return $this;
        }

        if ($name !== strtolower($name)) {
            throw new InvalidArgumentException('Package names must be lower case');
        }

        if (preg_match('/^([._])/', $name)) {
            throw new InvalidArgumentException('Package names must not start with . or _');
        }

        if (preg_match('/[~\'\"!()*]/', $name)) {
            throw new InvalidArgumentException('Package names must not include special characters');
        }

        if (strlen($name) > 214) {
            throw new InvalidArgumentException('Package names must not be longer than 214 characters');
        }

        if ($name !== trim($name)) {
            throw new InvalidArgumentException('Package names must not include whitespace');
        }

        $this->data['name'] = $name;

        return $this;
    }

    /**
     * Checks if workspace package is set
     *
     * @param string $path
     * @return bool
     */
    public function hasWorkspace(string $path): bool
    {
        return in_array($path, $this->data['workspaces']['packages']);
    }

    /**
     * Adds a new workspace, removes from ignored workspaces if present
     *
     * @param string $path
     * @return $this
     */
    public function addWorkspace(string $path): static
    {
        if (!in_array($path, $this->data['workspaces']['packages'])) {
            $this->data['workspaces']['packages'][] = $path;
        }

        if (($key = array_search($path, $this->data['workspaces']['ignoredPackages'])) !== false) {
            // remove the package from ignored workspaces
            unset($this->data['workspaces']['ignoredPackages'][$key]);
            // reset keys
            $this->data['workspaces']['ignoredPackages'] = array_values($this->data['workspaces']['ignoredPackages']);
        }

        // Sort the packages
        asort($this->data['workspaces']['packages']);
        $this->data['workspaces']['packages'] = array_values($this->data['workspaces']['packages']);

        return $this;
    }

    /**
     * Removes a workspace
     *
     * @param string $path
     * @return $this
     */
    public function removeWorkspace(string $path): static
    {
        if (($key = array_search($path, $this->data['workspaces']['packages'])) !== false) {
            // remove the package from workspace packages
            unset($this->data['workspaces']['packages'][$key]);
            // reset keys
            $this->data['workspaces']['packages'] = array_values($this->data['workspaces']['packages']);
        }

        return $this;
    }

    /**
     * Check if package is ignored
     *
     * @param string $path
     * @return bool
     */
    public function hasIgnoredPackage(string $path): bool
    {
        return in_array($path, $this->data['workspaces']['ignoredPackages']);
    }

    /**
     * Adds an ignored package, removes from workspaces if present
     *
     * @param string $path
     * @return $this
     */
    public function addIgnoredPackage(string $path): static
    {
        if (!in_array($path, $this->data['workspaces']['ignoredPackages'])) {
            $this->data['workspaces']['ignoredPackages'][] = $path;
        }

        if (($key = array_search($path, $this->data['workspaces']['packages'])) !== false) {
            // remove the package from ignored workspaces
            unset($this->data['workspaces']['packages'][$key]);
            // reset keys
            $this->data['workspaces']['packages'] = array_values($this->data['workspaces']['packages']);
        }

        // Sort the packages
        asort($this->data['workspaces']['ignoredPackages']);
        $this->data['workspaces']['ignoredPackages'] = array_values($this->data['workspaces']['ignoredPackages']);


        return $this;
    }

    /**
     * Removes an ignored package
     *
     * @param string $path
     * @return $this
     */
    public function removeIgnoredPackage(string $path): static
    {
        if (($key = array_search($path, $this->data['workspaces']['ignoredPackages'])) !== false) {
            // remove the package from workspace packages
            unset($this->data['workspaces']['ignoredPackages'][$key]);
            // reset keys
            $this->data['workspaces']['ignoredPackages'] = array_values($this->data['workspaces']['ignoredPackages']);
        }

        return $this;
    }

    /**
     * Checks if package.json has a dependency
     *
     * @param string $package
     * @return bool
     */
    public function hasDependency(string $package): bool
    {
        return isset($this->data['dependencies'][$package]) || isset($this->data['devDependencies'][$package]);
    }

    /**
     * Adds a dependency, supports adding to `dependencies` or `devDependencies` based on `$dev` and allows moving if
     * `$overwrite` is set
     *
     * @param string $package
     * @param string $version
     * @param bool $dev
     * @param bool $overwrite
     * @return $this
     */
    public function addDependency(string $package, string $version, bool $dev = false, bool $overwrite = false): static
    {
        // If the dep is defined already, but we are not overwriting, then exit
        if (
            (isset($this->data['dependencies'][$package]) || isset($this->data['devDependencies'][$package]))
            && !$overwrite
        ) {
            return $this;
        }

        // Clear any existing settings because we are overwriting
        unset($this->data['dependencies'][$package], $this->data['devDependencies'][$package]);

        // Define the dep
        $this->data[$dev ? 'devDependencies' : 'dependencies'][$package] = $version;

        return $this;
    }

    /**
     * Removes a package from both `dependencies` and `devDependencies`
     *
     * @param string $package
     * @return $this
     */
    public function removeDependency(string $package): static
    {
        unset($this->data['dependencies'][$package], $this->data['devDependencies'][$package]);
        return $this;
    }

    /**
     * Returns the package.json contents as an array
     *
     * @return array
     */
    public function getContents(): array
    {
        return $this->data;
    }

    /**
     * Saves the contents to a file, if the object was init'ed with a path it will save to the path, or can be
     * overwritten with `$path`.
     *
     * @param string|null $path
     * @return int
     */
    public function save(?string $path = null): int
    {
        return File::put(
            $path ?? $this->path ?? throw new RuntimeException('Unable to save, no path given'),
            json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }
}
