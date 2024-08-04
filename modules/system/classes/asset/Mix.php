<?php

namespace System\Classes\Asset;

use Cms\Classes\Theme;
use Illuminate\Support\HtmlString;
use Winter\Storm\Support\Str;

class Mix
{
    public static function mix(string $path, string $manifestDirectory = ''): HtmlString|string
    {
        static $manifests = [];

        $theme = Theme::getActiveTheme();

        $themePath = $theme->getPath($theme->getDirName());

        $path = Str::start($path, '/');

        if ($manifestDirectory && ! str_starts_with($manifestDirectory, '/')) {
            $manifestDirectory = "/$manifestDirectory";
        } else {
            $manifestDirectory = Str::start($theme->getConfigValue('mix_manifest_path', '/'), '/');
        }

        $manifestPath = $themePath.rtrim($manifestDirectory, '/').'/mix-manifest.json';

        if (! isset($manifests[$manifestPath])) {
            if (! is_file($manifestPath)) {
                throw new \Exception('The Mix manifest does not exist.');
            }

            $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }

        $manifest = $manifests[$manifestPath];

        if (! isset($manifest[$path])) {
            $exception = new \Exception("Unable to locate Mix file: $path");

            if (! app('config')->get('app.debug')) {
                report($exception);

                return $path;
            } else {
                throw $exception;
            }
        }

        return new HtmlString($theme->assetUrl(ltrim($manifest[$path], '/')));
    }
}
