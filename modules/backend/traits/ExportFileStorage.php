<?php

namespace Backend\Traits;

use Backend\Facades\BackendAuth;
use Illuminate\Support\Facades\Lang;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Support\Facades\File;

/**
 * Scopes the files the export flows leave in the temporary directory to the backend user that
 * produced them.
 *
 * Those files are addressed by name alone, and the temporary directory is shared by every
 * controller implementing the ImportExportController behavior as well as by the theme archive
 * flows. A reader therefore has to decide for itself whose file a name belongs to: the behavior's
 * export[permissions] gate establishes that a user may export from a controller, which says
 * nothing about the ownership of a name posted back to it.
 *
 * Each owner gets a directory of their own, and a reader only ever resolves a name inside its own.
 *
 * @package winter\wn-backend-module
 */
trait ExportFileStorage
{
    /**
     * Returns a unique name for a new export file.
     *
     * The name is random rather than derived from the clock, so that knowing when an export was
     * taken does not narrow it.
     */
    protected static function makeExportFileName(): string
    {
        return 'oc' . bin2hex(random_bytes(16));
    }

    /**
     * Returns the path to write a new export file to for the active backend user, creating that
     * user's directory if this is their first one.
     */
    protected static function getExportFilePath(string $name): string
    {
        $directory = static::getExportFileDirectory();

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        return $directory . '/' . static::validateExportFileName($name);
    }

    /**
     * Returns the path of an existing export file belonging to the active backend user, refusing a
     * name that is malformed, that belongs to somebody else, or that is already gone.
     */
    protected static function findExportFilePath(string $name): string
    {
        $path = static::getExportFileDirectory() . '/' . static::validateExportFileName($name);

        if (!File::isFile($path)) {
            throw new ApplicationException(Lang::get('backend::lang.import_export.file_not_found_error'));
        }

        return $path;
    }

    /**
     * Returns the directory holding the export files of the active backend user.
     */
    protected static function getExportFileDirectory(): string
    {
        if (!$user = BackendAuth::getUser()) {
            throw new ApplicationException(Lang::get('backend::lang.import_export.file_owner_error'));
        }

        return temp_path('exports/' . $user->getKey());
    }

    /**
     * Keeps a name to the shape the export flows generate, which admits no path separators.
     */
    protected static function validateExportFileName(string $name): string
    {
        if (!preg_match('/^oc[0-9a-z]+$/i', $name)) {
            throw new ApplicationException(Lang::get('backend::lang.import_export.file_not_found_error'));
        }

        return $name;
    }
}
