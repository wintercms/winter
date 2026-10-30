<?php

namespace System\Tests\Bootstrap;

use System\Tests\Concerns\MigratesDatabaseOnce;
use Winter\Storm\Database\Model;

/**
 * Registers Storm's "nicer" event hooks when it boots, because it declares one of them.
 */
class EventCountingModel extends Model
{
    public static int $beforeSaveCalls = 0;

    protected $table = 'system_request_logs';

    protected $guarded = [];

    public function beforeSave()
    {
        static::$beforeSaveCalls++;
    }
}

/**
 * Guards `PluginTestCase::flushModelEventListeners()`, which only visits models that have booted.
 *
 * @package winter/wn-system-module
 */
class ModelEventListenerFlushTest extends PluginTestCase
{
    use MigratesDatabaseOnce;

    public function testABootedModelsListenersAreForgotten(): void
    {
        new EventCountingModel;

        $event = 'eloquent.saving: ' . EventCountingModel::class;
        $this->assertTrue($this->app['events']->hasListeners($event));

        $this->flushModelEventListeners();

        $this->assertFalse($this->app['events']->hasListeners($event));
    }

    public function testTheModelRegistersItsHooksAgainAfterAFlush(): void
    {
        new EventCountingModel;
        $this->flushModelEventListeners();

        EventCountingModel::$beforeSaveCalls = 0;
        (new EventCountingModel(['url' => '/after-flush']))->save();

        $this->assertSame(1, EventCountingModel::$beforeSaveCalls);
    }
}
