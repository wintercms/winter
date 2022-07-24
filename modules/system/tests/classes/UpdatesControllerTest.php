<?php

namespace System\Tests\Classes;

use System\Tests\Bootstrap\TestCase;
use System\Controllers\Updates;

class UpdatesControllerTest extends TestCase
{

    //
    // Tests
    //

    public function testGetPluginVersionFile()
    {
        $controller = $this->getMockBuilder(Updates::class)->disableOriginalConstructor()->getMock();

        $expectedVersions = [
            '1.5.1' => [
                'Improved signature with the Test::method()',
                'Translation updates.',
            ],
            '1.5.0' => [
                '!!! Another major update to fix several issues',
            ],
            '1.4.1' => [
                '!!! Major update here.',
            ],
            '1.3.2' => [
                'Added support for Translate plugin. Added some new languages.',
            ],
            '1.3.1' => [
                'Minor bug fix Please see changelog',
            ],
            '1.3.0' => [
                '!!! We\'ve refactored major parts of this plugin. Please see the website for more information.',
            ],
            '1.2.0' => [
                '!!! Security update - see: https://wintercms.com',
            ],
            '1.1.0' => [
                '!!! Drop support for blog settings',
            ],
            '1.0.5' => [
                'Create blog settings table',
                'Another update message',
                'Yet one more update message',
            ],
            '1.0.4' => [
                'Another fix'
            ],
            '1.0.3' => [
                'Bug fix update that uses no scripts'
            ],
            '1.0.2' => [
                'Added some stuff',
            ],
            '1.0.1' => [
                'Added some upgrade file and some "seeding"',
                'some_seeding_file.php' //does not exist
            ],
        ];

        $versions = self::callProtectedMethod(
            $controller,
            'getPluginVersionFile',
            [
                base_path() . '/modules/system/tests/fixtures/plugins/winter/tester/',
                'updates/version.yaml'
            ]
        );

        $this->assertNotNull($versions);
        $this->assertEquals($expectedVersions, $versions);
    }
}
