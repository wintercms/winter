<?php

namespace System\Traits;

use Illuminate\Support\Facades\Lang;
use Winter\Storm\Exception\ApplicationException;
use Winter\Storm\Filesystem\Zip;

trait InteractsWithZip
{
    /**
     * Extract the provided archive
     *
     * @throws ApplicationException if the archive failed to extract
     */
    public function extractArchive(string $archive, string $destination): void
    {
        if (!Zip::extract($archive, $destination)) {
            throw new ApplicationException(Lang::get('system::lang.zip.extract_failed', ['file' => $archive]));
        }

        @unlink($archive);
    }

    public function packArchive(string $src, string $destination): string
    {
        if (!Zip::make($destination, $src)) {
            throw new ApplicationException(Lang::get('system::lang.zip.pack_failed', ['file' => $src]));
        }

        return $destination;
    }
}
