<?php

namespace System\Classes\Extensions\Traits;

use System\Classes\VersionYamlProcessor;
use Winter\Storm\Support\Facades\File;
use Winter\Storm\Support\Facades\Yaml;
use Winter\Storm\Support\Str;

/**
 * Must be implemented on a Winter\Storm\Foundation\Extension\WinterExtension instance
 */
trait HasVersionFile
{
    /**
     * Gets the contents of the plugin's updates/version.yaml file and normalizes the results
     */
    protected function getVersionsFromYaml(string $versionFile, bool $includeScripts = true): array
    {
        if (!File::isFile($versionFile)) {
            return [];
        }

        $updates = Yaml::withProcessor(new VersionYamlProcessor, function ($yaml) use ($versionFile) {
            return (array) $yaml->parseFile($versionFile);
        });

        uksort($updates, function ($a, $b) {
            return version_compare($a, $b);
        });

        $versions = [];
        foreach ($updates as $version => $details) {
            if (!is_array($details)) {
                $details = [$details];
            }

            if (!$includeScripts) {
                // Filter out valid update scripts
                $details = array_values(array_filter($details, function ($string) {
                    return !Str::endsWith($string, '.php') || !File::exists($this->getPath() . '/updates/' . $string);
                }));
            }

            $versions[$version] = $details;
        }

        return $versions;
    }
}
