<?php

namespace System\Tests;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request;
use System\Classes\PluginManager;
use System\ServiceProvider;
use System\Tests\Bootstrap\TestCase;

/**
 * Covers which request paths switch plugin initialization off.
 *
 * registerPrivilegedActions() runs from register(), before the router exists, so it
 * recognises the privileged contexts by their request path. Every path it matches has to be
 * one the route it stands for actually serves: anything else is served by another route -
 * in practice the CMS catch-all - and has to initialize plugins as usual.
 */
class ServiceProviderPrivilegedActionsTest extends TestCase
{
    /**
     * @dataProvider requestPathProvider
     */
    public function testOnlyThePathsTheNamedRoutesServeDisablePluginInit(string $path, bool $expected)
    {
        $this->assertEquals($expected, $this->pluginInitDisabledFor($path), $path);
    }

    public static function requestPathProvider(): array
    {
        return [
            // The asset combiner is `combine/{file}`, and `{file}` is a single segment.
            'a combined asset' => ['/combine/5f4dcc3b5aa765d6-1771200000', true],
            'a combined asset, trailing slash' => ['/combine/5f4dcc3b5aa765d6-1771200000/', true],
            'a path below the combiner' => ['/combine/x/members/dashboard', false],
            'a two segment path below the combiner' => ['/combine/x/dashboard', false],
            'an empty segment below the combiner' => ['/combine//dashboard', false],
            'a separator the combiner route decodes' => ['/combine/x%2Fdashboard', false],
            'an encoded combiner url the route still matches' => ['/%63ombine/x', true],
            'the combiner in another case' => ['/COMBINE/x', false],
            'the combiner without a file' => ['/combine', false],
            'a page named like the combiner' => ['/combiner', false],

            // Backend contexts that have to work with plugins uninitialized.
            'the backend sign in page' => ['/backend/backend/auth/signin', true],
            'a backend password reset' => ['/backend/backend/auth/reset/1/abc', true],
            'the update gateway' => ['/backend/system/updates', true],
            'the installer' => ['/backend/system/install', true],

            // Ordinary front end requests.
            'the homepage' => ['/', false],
            'a CMS page' => ['/members/dashboard', false],
        ];
    }

    /**
     * Runs the real decision for a request path and reports what it did to the flag.
     */
    protected function pluginInitDisabledFor(string $path): bool
    {
        $request = Request::getFacadeRoot();
        $noInit = PluginManager::$noInit;

        try {
            PluginManager::$noInit = false;
            Request::swap(HttpRequest::create($path));

            self::callProtectedMethod(new ServiceProvider($this->app), 'registerPrivilegedActions');

            return PluginManager::$noInit;
        } finally {
            Request::swap($request);
            PluginManager::$noInit = $noInit;
        }
    }
}
