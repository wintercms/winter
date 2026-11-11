<?php

namespace System\Tests;

use Config;
use Db;
use Event;
use Illuminate\Log\Events\MessageLogged;
use Log;
use Monolog\Handler\NullHandler;
use System\Classes\SilentLogChannel;
use System\ServiceProvider;
use System\Tests\Bootstrap\PluginTestCase;
use System\Tests\Concerns\MigratesDatabaseOnce;

class ServiceProviderTest extends PluginTestCase
{
    use MigratesDatabaseOnce;

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

    public function testNullDeprecationChannelDoesNotReachEventLog()
    {
        $logged = [];
        Event::listen(MessageLogged::class, function (MessageLogged $event) use (&$logged) {
            $logged[] = $event->message;
        });

        Log::channel('deprecations')->warning('Deprecated thing');
        Log::warning('Real warning');

        $this->assertSame(['Real warning'], $logged);
    }

    public function testDeprecationChannelIsSilencedOnlyWhenNull()
    {
        $silenced = [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
            'tap' => [SilentLogChannel::class],
        ];

        foreach ([null, 'null', ['channel' => 'null'], ['trace' => true]] as $option) {
            $this->assertSame($silenced, $this->resolveDeprecationChannel($option), json_encode($option));
        }

        foreach (['single', ['channel' => 'daily']] as $option) {
            $this->assertNull($this->resolveDeprecationChannel($option), json_encode($option));
        }

        // An app-defined deprecations channel is left alone.
        $custom = ['driver' => 'single', 'path' => storage_path('logs/deprecations.log')];
        $this->assertSame($custom, $this->resolveDeprecationChannel('null', $custom));
    }

    protected function resolveDeprecationChannel($option, ?array $channel = null): ?array
    {
        Config::set('logging.deprecations', $option);
        Config::set('logging.channels.deprecations', $channel);

        $method = new \ReflectionMethod(ServiceProvider::class, 'silenceNullDeprecationChannel');
        $method->setAccessible(true);
        $method->invoke($this->app->getProvider(ServiceProvider::class));

        return Config::get('logging.channels.deprecations');
    }
}
