<?php

namespace System\Tests\Classes;

use System\Classes\MediaLibraryItem;
use System\Tests\Bootstrap\TestCase;

class MediaLibraryItemTest extends TestCase
{
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

    /**
     * A file touched just after local midnight is still on the previous day in UTC, so reading its
     * timestamp as UTC lists it under the wrong date.
     */
    public function testLastModifiedAsStringUsesTheDefaultTimezone()
    {
        // 2023-11-14 23:30:00 UTC, which is 2023-11-15 00:30:00 in Europe/Brussels
        $item = new MediaLibraryItem('/test.jpg', 100, 1700004600, MediaLibraryItem::TYPE_FILE, null);

        $this->assertSame('Nov 15, 2023', $item->lastModifiedAsString());
    }
}
