<?php

namespace System\Tests\Classes;

use System\Tests\Bootstrap\TestCase;
use System\Classes\VersionManager;

class VersionManagerTest extends TestCase
{

    public function setUp() : void
    {
        parent::setUp();

        include_once base_path() . '/modules/system/tests/fixtures/plugins/winter/tester/Plugin.php';
        include_once base_path() . '/modules/system/tests/fixtures/plugins/winter/sample/Plugin.php';
        include_once base_path() . '/modules/system/tests/fixtures/plugins/winter/noupdates/Plugin.php';
    }

    //
    // Tests
    //

    public function testGetLatestFileVersion()
    {
        $manager = VersionManager::instance();
        $result = self::callProtectedMethod($manager, 'getLatestFileVersion', ['\Winter\\Tester']);

        $this->assertNotNull($result);
        $this->assertEquals('1.5.1', $result);
    }

    public function testGetFileVersions()
    {
        $manager = VersionManager::instance();
        $result = self::callProtectedMethod($manager, 'getFileVersions', ['\Winter\\Tester']);

        $this->assertCount(13, $result);
        $this->assertArrayHasKey('1.0.1', $result);
        $this->assertArrayHasKey('1.0.2', $result);
        $this->assertArrayHasKey('1.0.3', $result);
        $this->assertArrayHasKey('1.0.4', $result);
        $this->assertArrayHasKey('1.0.5', $result);
        $this->assertArrayHasKey('1.1.0', $result);
        $this->assertArrayHasKey('1.2.0', $result);
        $this->assertArrayHasKey('1.3.0', $result);
        $this->assertArrayHasKey('1.3.1', $result);
        $this->assertArrayHasKey('1.3.2', $result);
        $this->assertArrayHasKey('1.4.1', $result);
        $this->assertArrayHasKey('1.5.0', $result);
        $this->assertArrayHasKey('1.5.1', $result);

        $sample = $result['1.0.1'];
        $this->assertEquals('Added some upgrade file and some "seeding"', $sample[0]);

        $sample = $result['1.1.0'];
        $this->assertEquals('!!! Drop support for blog settings', $sample[0]);
        $this->assertEquals('drop_blog_settings_table.php', $sample[1]);

        $sample = $result['1.2.0'];
        $this->assertEquals('!!! Security update - see: https://wintercms.com', $sample[0]);

        $sample = $result['1.3.0'];
        $this->assertEquals('!!! We\'ve refactored major parts of this plugin. Please see the website for more information.', $sample);

        $sample = $result['1.3.1'];
        $this->assertEquals('Minor bug fix Please see changelog', $sample[0]);
        $this->assertEquals('fix_database.php', $sample[1]);

        $sample = $result['1.3.2'];
        $this->assertEquals('Added support for Translate plugin. Added some new languages.', $sample);

        $sample = $result['1.5.0'];
        $this->assertEquals('!!! Another major update to fix several issues', $sample);

        $sample = $result['1.5.1'];
        $this->assertEquals('Improved signature with the Test::method()', $sample[0]);
        $this->assertEquals('Translation updates.', $sample[1]);

        /*
         * Test junk file
         */
        $result = self::callProtectedMethod($manager, 'getFileVersions', ['\Winter\\Sample']);
        $this->assertCount(5, $result);
        $this->assertArrayHasKey('junk', $result);
        $this->assertArrayHasKey('1', $result);
        $this->assertArrayHasKey('1.0.*', $result);
        $this->assertArrayHasKey('1.0.x', $result);
        $this->assertArrayHasKey('10.3', $result);

        $sample = array_shift($result);
        $comment = array_shift($sample);
        $this->assertEquals("JUNK JUNK JUNK", $comment);

        /*
         * Test empty file
         */
        $result = self::callProtectedMethod($manager, 'getFileVersions', ['\Winter\\NoUpdates']);
        $this->assertEmpty($result);
    }

    public function testGetNewFileVersions()
    {
        $manager = VersionManager::instance();
        $result = self::callProtectedMethod($manager, 'getNewFileVersions', ['\Winter\\Tester', '1.0.3']);

        $this->assertCount(10, $result);
        $this->assertArrayHasKey('1.0.4', $result);
        $this->assertArrayHasKey('1.0.5', $result);
        $this->assertArrayHasKey('1.1.0', $result);
        $this->assertArrayHasKey('1.2.0', $result);
        $this->assertArrayHasKey('1.3.0', $result);
        $this->assertArrayHasKey('1.3.1', $result);
        $this->assertArrayHasKey('1.3.2', $result);
        $this->assertArrayHasKey('1.4.1', $result);
        $this->assertArrayHasKey('1.5.0', $result);
        $this->assertArrayHasKey('1.5.1', $result);

        /*
         * When at version 0, should return everything
         */
        $manager = VersionManager::instance();
        $result = self::callProtectedMethod($manager, 'getNewFileVersions', ['\Winter\\Tester']);

        $this->assertCount(13, $result);
        $this->assertArrayHasKey('1.0.1', $result);
        $this->assertArrayHasKey('1.0.2', $result);
        $this->assertArrayHasKey('1.0.3', $result);
        $this->assertArrayHasKey('1.0.4', $result);
        $this->assertArrayHasKey('1.0.5', $result);
        $this->assertArrayHasKey('1.1.0', $result);
        $this->assertArrayHasKey('1.2.0', $result);
        $this->assertArrayHasKey('1.3.0', $result);
        $this->assertArrayHasKey('1.3.1', $result);
        $this->assertArrayHasKey('1.3.2', $result);
        $this->assertArrayHasKey('1.4.1', $result);
        $this->assertArrayHasKey('1.5.0', $result);
        $this->assertArrayHasKey('1.5.1', $result);
    }

    /**
     * @dataProvider versionInfoProvider
     *
     * @param $versionInfo
     * @param $expectedComments
     * @param $expectedScripts
     */
    public function testExtractScriptsAndComments($versionInfo, $expectedComments, $expectedScripts)
    {
        $manager = VersionManager::instance();
        list($comments, $scripts) = self::callProtectedMethod($manager, 'extractScriptsAndComments', [$versionInfo]);

        $this->assertIsArray($comments);
        $this->assertIsArray($scripts);

        $this->assertEquals($expectedComments, $comments);
        $this->assertEquals($expectedScripts, $scripts);
    }

    public function versionInfoProvider()
    {
        return [
            [
                'A single update comment string',
                [
                    'A single update comment string'
                ],
                []
            ],
            [
                [
                    'A classic update comment string followed by script',
                    'update_script.php'
                ],
                [
                    'A classic update comment string followed by script'
                ],
                [
                    'update_script.php'
                ]
            ],
            [
                [
                    'scripts_can_go_first.php',
                    'An update comment string after the script',
                ],
                [
                    'An update comment string after the script'
                ],
                [
                    'scripts_can_go_first.php'
                ]
            ],
            [
                [
                    'scripts_can_go_first.php',
                    'An update comment string after the script',
                    'scripts_can_go_anywhere.php',
                ],
                [
                    'An update comment string after the script'
                ],
                [
                    'scripts_can_go_first.php',
                    'scripts_can_go_anywhere.php'
                ]
            ],
            [
                [
                    'scripts_can_go_first.php',
                    'The first update comment',
                    'scripts_can_go_anywhere.php',
                    'The second update comment',
                ],
                [
                    'The first update comment',
                    'The second update comment'
                ],
                [
                    'scripts_can_go_first.php',
                    'scripts_can_go_anywhere.php'
                ]
            ],
            [
                [
                    'file.name.with.dots.php',
                    'The first update comment',
                    '1.0.2.scripts_can_go_anywhere.php',
                    'The second update comment',
                ],
                [
                    'The first update comment',
                    'The second update comment'
                ],
                [
                    'file.name.with.dots.php',
                    '1.0.2.scripts_can_go_anywhere.php'
                ]
            ],
            [
                [
                    'subdirectory/file.name.with.dots.php',
                    'The first update comment',
                    'subdirectory\1.0.2.scripts_can_go_anywhere.php',
                    'The second update comment',
                ],
                [
                    'The first update comment',
                    'The second update comment'
                ],
                [
                    'subdirectory/file.name.with.dots.php',
                    'subdirectory\1.0.2.scripts_can_go_anywhere.php'
                ]
            ]
        ];
    }
}
