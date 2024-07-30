<?php

namespace System\Tests\Classes;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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
                    'Plugin.php',
                ],
            ],
            'single_plugin_nested' => [
                'zipFile' => 'single-plugin-nested.zip',
                'expectedPaths' => [
                    'testa/Plugin.php',
                ],
            ],
            'multiple_plugins_no_author' => [
                'zipFile' => 'multiple-plugins-no-author.zip',
                'expectedPaths' => [
                    'testa/Plugin.php',
                    'testb/Plugin.php',
                    'testc/Plugin.php',
                ],
            ],
            'multiple_authors' => [
                'zipFile' => 'multiple-authors.zip',
                'expectedPaths' => [
                    'mix/testa/Plugin.php',
                    'mix/testb/Plugin.php',
                    'mix/testc/Plugin.php',
                    'testvendor/test/Plugin.php',
                ],
            ],
            'entire_plugins_directory' => [
                'zipFile' => 'entire-plugins-directory.zip',
                'expectedPaths' => [
                    'plugins/mix/testa/Plugin.php',
                    'plugins/mix/testb/Plugin.php',
                    'plugins/mix/testc/Plugin.php',
                    'plugins/testvendor/test/Plugin.php',
                ],
            ],
        ];
    }

    /**
     * @dataProvider pluginExtractionDataProvider
     */
    public function testExtractPlugins($zipFile, $expectedPaths)
    {
        $zipPath = base_path('modules/system/tests/fixtures/plugin-archives/' . $zipFile);
        $tempZipPath = temp_path('packages/'. pathinfo($zipPath, PATHINFO_BASENAME));
        $tempPath = temp_path('packages/'. pathinfo($zipPath, PATHINFO_FILENAME) . '_contents') . '/';

        File::makeDirectory(temp_path('packages/'), 0755, true, true);
        File::copy($zipPath, $tempZipPath);

        $this->manager->extractArchive($tempZipPath, $tempPath);

        $pluginPaths = $this->findPluginFiles($tempPath);

        // Normalize paths to make sure comparison is correct regardless of OS
        $normalizedPluginPaths = array_map('realpath', $pluginPaths);
        $normalizedExpectedPaths = array_map(function($path) use ($tempPath) {
            return $tempPath . $path;
        }, $expectedPaths);

        $this->assertEqualsCanonicalizing($normalizedExpectedPaths, $normalizedPluginPaths);

        // @TODO: Cleanup after self
    }

    /**
     * @TODO:
     * - Move this into a utility class (UpdateManager?)
     * - Include logic for detecting the plugin author / name from the plugin class namespace
     * - Maybe include logic for determining what order the plugins should be installed in
     * - Rename plugins in the fixture files to not conflict with the testing plugins when we
     * add cases for testing the actual installation of plugins
     */
    protected function findPluginFiles($path)
    {
        $pluginFiles = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === 'Plugin.php') {
                $pluginFiles[] = $file->getPathname();
            }
        }

        return $pluginFiles;
    }
}
