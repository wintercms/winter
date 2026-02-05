<?php

namespace System\Classes\Extensions\Source;

use Winter\Storm\Exception\ApplicationException;

class MarketSource extends ExtensionSource
{
    /**
     * @throws ApplicationException
     */
    public function __construct(
        string $type,
        ?string $code = null,
        ?string $composerPackage = null,
        ?string $path = null
    ) {
        parent::__construct(static::SOURCE_MARKET, $type, $code, $composerPackage, $path);
    }
}
