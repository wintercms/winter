<?php

namespace System\Classes\Extensions;

use Cms\Classes\Theme;
use Illuminate\Support\Facades\Storage;
use System\Traits\InteractsWithZip;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Foundation\Extension\WinterExtension;
use Winter\Storm\Support\ModuleServiceProvider;
use Winter\Storm\Support\Traits\Singleton;

class Preserver
{
    use Singleton;
    use InteractsWithZip;

    public const ROOT_PATH = 'archive';

    protected array $classMap = [
        ModuleServiceProvider::class => 'modules',
        PluginBase::class => 'plugins',
        Theme::class => 'themes',
    ];

    /**
     * @throws ApplicationException
     */
    public function store(WinterExtension $extension): string
    {
        $this->ensureDirectory(static::ROOT_PATH);

        if (!($type = $this->resolveType($extension))) {
            throw new ApplicationException('Unable to resolve class type: ' . $extension::class);
        }

        $this->ensureDirectory(static::ROOT_PATH . DIRECTORY_SEPARATOR . $type);

        $extensionArchiveDir = sprintf(
            '%s%4$s%s%4$s%s',
            static::ROOT_PATH,
            $type,
            $extension->getIdentifier(),
            DIRECTORY_SEPARATOR
        );

        $this->ensureDirectory($extensionArchiveDir);

        return $this->packArchive(
            $extension->getPath(),
            Storage::path($extensionArchiveDir . DIRECTORY_SEPARATOR . $extension->getVersion())
        );
    }

    protected function ensureDirectory(string $path): bool
    {
        if (!Storage::directoryExists($path)) {
            return Storage::makeDirectory($path);
        }

        return true;
    }

    public function resolveType(WinterExtension $extension): ?string
    {
        foreach ($this->classMap as $class => $type) {
            if ($extension instanceof $class) {
                return $type;
            }
        }

        return null;
    }
}
