<?php

namespace System\Tests;

use Illuminate\Support\Facades\Log;
use System\Tests\Bootstrap\PluginTestCase;
use Winter\Storm\Support\Facades\Config;
use Winter\Storm\Support\Facades\DB;

class ServiceProviderTest extends PluginTestCase
{
    /**
     * Test the registerLogging method
     *
     * @return void
     */
    public function testRegisterLogging()
    {
        // Verify that calling the Log::info() method and passing in details stores those details in the event log table
        $message = 'This is a test log message';
        $details = [
            'key' => 'Dummy value',
        ];
        Log::info($message, $details);
        $latestLog = Db::table('system_event_logs')->latest()->first();
        $this->assertEquals($message, $latestLog->message);
        $this->assertEquals($details, json_decode($latestLog->details, true));
    }

    public function testValidateFilesystemConfigPolyfillsMissingDisks()
    {
        // Backup global config
        $originalDisks = Config::get('filesystems.disks');
        $originalCmsStorage = Config::get('cms.storage');

        try {
            // Minimal disk config with missing system disks
            Config::set('filesystems.disks', [
                'local' => [
                    'driver' => 'local',
                    'root' => storage_path('app'),
                    'url' => null,
                ],
            ]);

            // Legacy cms.php config
            Config::set('cms.storage', [
                'uploads' => [
                    'disk' => 'local',
                    'folder' => 'uploads',
                    'path' => '/storage/app/uploads',
                    'temporaryUrlTTL' => 3600,
                ],
                'media' => [
                    'disk' => 'local',
                    'folder' => 'media',
                    'path' => '/storage/app/media',
                ],
                'resized' => [
                    'disk' => 'local',
                    'folder' => 'resized',
                    'path' => '/storage/app/resized',
                ],
            ]);

            // Call method
            $provider = new \System\ServiceProvider(app());
            $method = new \ReflectionMethod($provider, 'validateFilesystemConfig');
            $method->setAccessible(true);
            $method->invoke($provider);

            // Assertions
            $this->assertEquals('/storage/app', Config::get('filesystems.disks.local.url'));

            $this->assertEquals([
                'driver' => 'scoped',
                'disk' => 'local',
                'prefix' => 'uploads',
                'url' => '/storage/app/uploads',
                'temporaryUrlTTL' => 3600,
            ], Config::get('filesystems.disks.uploads-public'));

            $this->assertEquals([
                'driver' => 'scoped',
                'disk' => 'local',
                'prefix' => 'media',
                'url' => '/storage/app/media',
            ], Config::get('filesystems.disks.media'));

            $this->assertEquals([
                'driver' => 'scoped',
                'disk' => 'local',
                'prefix' => 'resized',
                'url' => '/storage/app/resized',
            ], Config::get('filesystems.disks.resized'));
        } finally {
            // Restore config
            Config::set('filesystems.disks', $originalDisks);
            Config::set('cms.storage', $originalCmsStorage);
        }
    }
}
