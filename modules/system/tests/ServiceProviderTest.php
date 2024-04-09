<?php

namespace System\Tests;

use Db;
use Log;
use System\Tests\Bootstrap\PluginTestCase;

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
}
