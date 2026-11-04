<?php

namespace Backend\Tests\Helpers;

use System\Tests\Bootstrap\TestCase;
use Backend\Helpers\Backend;
use Backend\Helpers\Exception\DecompileException;

class BackendHelperTest extends TestCase
{
    /**
     * 2023-11-14 23:30:00 UTC, which is 2023-11-15 00:30:00 in Europe/Brussels.
     * The two fall on different calendar days, so a timezone slip changes the date as well as the time.
     */
    const TIMESTAMP = 1700004600;
    const LOCAL_DATETIME = '2023-11-15 00:30:00';

    /**
     * @var string The default timezone in place before the test ran.
     */
    protected $originalTimezone;

    public function setUp(): void
    {
        parent::setUp();

        $this->originalTimezone = date_default_timezone_get();

        date_default_timezone_set('Europe/Brussels');
    }

    public function tearDown(): void
    {
        date_default_timezone_set($this->originalTimezone);

        parent::tearDown();
    }

    public function testDecompileAssets()
    {
        $backendHelper = new Backend;
        $assets = $backendHelper->decompileAsset('modules/backend/tests/fixtures/assets/compilation.js');

        $this->assertCount(2, $assets);
        $this->assertStringContainsString('file1.js', $assets[0]);
        $this->assertStringContainsString('file2.js', $assets[1]);
    }

    public function testDecompileMissingFile()
    {
        $this->expectException(DecompileException::class);

        $backendHelper = new Backend;
        $assets = $backendHelper->decompileAsset('modules/backend/tests/fixtures/assets/missing.js');
    }

    /**
     * The datetime attribute is read back by winter.datetime.js as a wall time in the application
     * timezone, so a timestamp written to it as UTC is displayed an offset out.
     */
    public function testDateTimeWritesTimestampsInTheApplicationTimezone()
    {
        $backendHelper = new Backend;

        $this->assertStringContainsString(
            'datetime="' . static::LOCAL_DATETIME . '"',
            $backendHelper->dateTime(static::TIMESTAMP)
        );
    }

    public function testDateTimeRendersATimestampAndItsLocalDatetimeIdentically()
    {
        $backendHelper = new Backend;

        $this->assertSame(
            $backendHelper->dateTime(static::LOCAL_DATETIME, ['formatAlias' => 'dateTimeMin']),
            $backendHelper->dateTime(static::TIMESTAMP, ['formatAlias' => 'dateTimeMin'])
        );
    }
}
