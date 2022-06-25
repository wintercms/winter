<?php

return [

    /*
     * Laravel providers
     */
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
    Illuminate\Bus\BusServiceProvider::class,
    Illuminate\Cache\CacheServiceProvider::class,
    Illuminate\Cookie\CookieServiceProvider::class,
    Illuminate\Encryption\EncryptionServiceProvider::class,
    Illuminate\Foundation\Providers\FoundationServiceProvider::class,
    Illuminate\Hashing\HashServiceProvider::class,
    Illuminate\Pagination\PaginationServiceProvider::class,
    Illuminate\Pipeline\PipelineServiceProvider::class,
    Illuminate\Queue\QueueServiceProvider::class,
    Illuminate\Session\SessionServiceProvider::class,
    Illuminate\View\ViewServiceProvider::class,
    Laravel\Tinker\TinkerServiceProvider::class,

    /*
     * Winter Storm providers
     */
    Winter\Storm\Foundation\Providers\ConsoleSupportServiceProvider::class,
    Winter\Storm\Database\DatabaseServiceProvider::class,
    Winter\Storm\Halcyon\HalcyonServiceProvider::class,
    Winter\Storm\Filesystem\FilesystemServiceProvider::class,
    Winter\Storm\Parse\ParseServiceProvider::class,
    Winter\Storm\Html\HtmlServiceProvider::class,
    Winter\Storm\Html\UrlServiceProvider::class,
    Winter\Storm\Network\NetworkServiceProvider::class,
    Winter\Storm\Flash\FlashServiceProvider::class,
    Winter\Storm\Mail\MailServiceProvider::class,
    Winter\Storm\Argon\ArgonServiceProvider::class,
    Winter\Storm\Redis\RedisServiceProvider::class,
    Winter\Storm\Validation\ValidationServiceProvider::class,

];
