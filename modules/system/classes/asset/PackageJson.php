<?php

namespace System\Classes\Asset;

use InvalidArgumentException;
use RuntimeException;
use Winter\Storm\Support\Facades\File;

/**
 * PHP based interface for interacting with package.json files. This allows for modification of deps, devDeps, package
 * name and workspaces.
 *
 * @package winter\wn-system-module
 * @author Jack Wilkinson <me@jackwilky.com>
 * @author Winter CMS
 */
class PackageJson
{
    /**
     * The contents of the package.json being modified
     */
    protected array $data = [];

    /**
     * Create a new instance with optional path, loads file if file already exists
     * @throws \JsonException
     */
    public function __construct(
        protected ?string $path = null
    ) {
        if (File::exists($this->path)) {
            // Test the json to insure it's valid
            $json = json_decode(File::get($this->path), JSON_OBJECT_AS_ARRAY);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \JsonException('The contents of the file "' . $this->path . '" is not valid json.');
            }

            $this->data = $json;
        }
    }

    /**
     * Returns the package name if set
     */
    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    /**
     * Sets the package name, throws `InvalidArgumentException` on invalid name
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
     */
    public function hasWorkspace(string $path): bool
    {
        return in_array($path, $this->data['workspaces']['packages'] ?? []);
    }

    /**
     * Adds a new workspace, removes from ignored workspaces if present
     */
    public function addWorkspace(string $path): static
    {
        if (!in_array($path, $this->data['workspaces']['packages'] ?? [])) {
            $this->data['workspaces']['packages'][] = $path;
        }

        if (($key = array_search($path, $this->data['workspaces']['ignoredPackages'] ?? [])) !== false) {
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
     */
    public function removeWorkspace(string $path): static
    {
        if (($key = array_search($path, $this->data['workspaces']['packages'] ?? [])) !== false) {
            // remove the package from workspace packages
            unset($this->data['workspaces']['packages'][$key]);
            // reset keys
            $this->data['workspaces']['packages'] = array_values($this->data['workspaces']['packages']);
        }

        return $this;
    }

    /**
     * Check if package is ignored
     */
    public function hasIgnoredPackage(string $path): bool
    {
        return in_array($path, $this->data['workspaces']['ignoredPackages'] ?? []);
    }

    /**
     * Adds an ignored package, removes from workspaces if present
     */
    public function addIgnoredPackage(string $path): static
    {
        if (!in_array($path, $this->data['workspaces']['ignoredPackages'] ?? [])) {
            $this->data['workspaces']['ignoredPackages'][] = $path;
        }

        if (($key = array_search($path, $this->data['workspaces']['packages'] ?? [])) !== false) {
            // remove the package from ignored workspaces
            unset($this->data['workspaces']['packages'][$key]);
            // reset keys
            $this->data['workspaces']['packages'] = array_values($this->data['workspaces']['packages']);
        }

        // Sort the packages
        asort($this->data['workspaces']['ignoredPackages']);
        $this->data['workspaces']['ignoredPackages'] = array_values($this->data['workspaces']['ignoredPackages'] ?? []);

        return $this;
    }

    /**
     * Removes an ignored package
     */
    public function removeIgnoredPackage(string $path): static
    {
        if (($key = array_search($path, $this->data['workspaces']['ignoredPackages'] ?? [])) !== false) {
            // remove the package from workspace packages
            unset($this->data['workspaces']['ignoredPackages'][$key]);
            // reset keys
            $this->data['workspaces']['ignoredPackages'] = array_values($this->data['workspaces']['ignoredPackages']);
        }

        return $this;
    }

    /**
     * Checks if package.json has a dependency
     */
    public function hasDependency(string $package): bool
    {
        return isset($this->data['dependencies'][$package]) || isset($this->data['devDependencies'][$package]);
    }

    /**
     * Adds a dependency, supports adding to `dependencies` or `devDependencies` based on `$dev` and allows moving if
     * `$overwrite` is set
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
        $this->removeDependency($package);

        // Define the dep
        $this->data[$dev ? 'devDependencies' : 'dependencies'][$package] = $version;

        return $this;
    }

    /**
     * Removes a package from both `dependencies` and `devDependencies`
     */
    public function removeDependency(string $package): static
    {
        unset($this->data['dependencies'][$package], $this->data['devDependencies'][$package]);
        return $this;
    }

    /**
     * Returns if a script exists
     */
    public function hasScript(string $name): bool
    {
        return isset($this->data['scripts'][$name]);
    }

    /**
     * Returns the value of a script by name
     */
    public function getScript(string $name): ?string
    {
        return $this->data['scripts'][$name] ?? null;
    }

    /**
     * Adds a script
     */
    public function addScript(string $name, string $script): static
    {
        $this->data['scripts'][$name] = $script;
        return $this;
    }

    /**
     * Removes a script by name
     */
    public function removeScript(string $name): static
    {
        unset($this->data['scripts'][$name]);
        return $this;
    }

    /**
     * Returns the package.json contents as an array
     */
    public function getContents(): array
    {
        return $this->data;
    }

    /**
     * Returns the path of the package.json if set
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Saves the contents to a file, if the object was init'ed with a path it will save to the path, or can be
     * overwritten with `$path`.
     */
    public function save(?string $path = null): int
    {
        return File::put(
            $path ?? $this->path ?? throw new RuntimeException('Unable to save, no path given'),
            json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }
}
