<?php

namespace System\Tests\Classes;

use System\Tests\Bootstrap\TestCase;
use System\Classes\UpdateManager;
use Winter\Storm\Support\Facades\File;

class UpdateManagerTest extends TestCase
{
    protected UpdateManager $manager;

    public function setUp() : void
    {
        parent::setUp();

        $this->manager = UpdateManager::instance();
    }

    /**
     * Data provider for testExtractPlugins.
     */
    public function pluginExtractionDataProvider(): array
    {
        return [
            'single_plugin_contents' => [
                'zipFile' => 'single-plugin-contents.zip',
                'expectedPaths' => [
                    'Extracted.TestA' => 'Plugin.php',
                ],
            ],
            'single_plugin_nested' => [
                'zipFile' => 'single-plugin-nested.zip',
                'expectedPaths' => [
                    'Extracted.TestA' => 'testa/Plugin.php',
                ],
            ],
            'multiple_plugins_no_author' => [
                'zipFile' => 'multiple-plugins-no-author.zip',
                'expectedPaths' => [
                    'Extracted.TestA' => 'testa/Plugin.php',
                    'Extracted.TestB' => 'testb/Plugin.php',
                    'Extracted.TestC' => 'testc/Plugin.php',
                ],
            ],
            'multiple_authors' => [
                'zipFile' => 'multiple-authors.zip',
                'expectedPaths' => [
                    'Expanded.Test' => 'expanded/test/Plugin.php',
                    'Extracted.TestA' => 'extracted/testa/Plugin.php',
                    'Extracted.TestB' => 'extracted/testb/Plugin.php',
                    'Extracted.TestC' => 'extracted/testc/Plugin.php',
                ],
            ],
            'entire_plugins_directory' => [
                'zipFile' => 'entire-plugins-directory.zip',
                'expectedPaths' => [
                    'Expanded.Test' => 'plugins/expanded/test/Plugin.php',
                    'Extracted.TestA' => 'plugins/extracted/testa/Plugin.php',
                    'Extracted.TestB' => 'plugins/extracted/testb/Plugin.php',
                    'Extracted.TestC' => 'plugins/extracted/testc/Plugin.php',
                ],
            ],
        ];
    }

    /**
     * @dataProvider pluginExtractionDataProvider
     */
    public function testExtractPlugins($zipFile, $expectedPaths)
    {
        // Reset temp directory
        File::deleteDirectory(temp_path('packages/'));
        File::makeDirectory(temp_path('packages/'), 0755, true, true);

        $zipPath = base_path('modules/system/tests/fixtures/plugin-archives/' . $zipFile);
        $tempZipPath = temp_path('packages/'. pathinfo($zipPath, PATHINFO_BASENAME));
        $tempPath = temp_path('packages/'. pathinfo($zipPath, PATHINFO_FILENAME) . '_contents') . '/';

        File::copy($zipPath, $tempZipPath);

        $this->manager->extractArchive($tempZipPath, $tempPath);

        $pluginPaths = $this->manager->findPluginsInPath($tempPath);

        // Normalize paths to make sure comparison is correct regardless of OS
        $normalizedPluginPaths = array_map('realpath', $pluginPaths);
        $normalizedExpectedPaths = array_map(function ($path) use ($tempPath) {
            return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $tempPath . $path);
        }, $expectedPaths);

        $this->assertEquals($normalizedExpectedPaths, $normalizedPluginPaths);

        // @TODO: Cleanup after self
    }

    /**
     * @TODO:
     * - Move this into a utility class (UpdateManager?)
     * - √ - Include logic for detecting the plugin author / name from the plugin class namespace
     * - Maybe include logic for determining what order the plugins should be installed in
     * - √ - Rename plugins in the fixture files to not conflict with the testing plugins when we
     * add cases for testing the actual installation of plugins
     */
}
