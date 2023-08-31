<?php

namespace System\Tests\Classes;

use System\Tests\Bootstrap\TestCase;
use League\Flysystem\FilesystemInterface;
use System\Classes\MediaLibrary;

class MediaLibraryTest extends TestCase
{
    public function setUp(): void
    {
        MediaLibrary::forgetInstance();
        parent::setUp();
    }

    public function tearDown(): void
    {
        $this->removeMedia();
        parent::tearDown();
    }

    public function invalidPathsProvider()
    {
        return [
            ['./file'],
            ['../secret'],
            ['.../secret'],
            ['/../secret'],
            ['/.../secret'],
            ['/secret/..'],
            ['file/../secret'],
            ['file/..'],
            ['......./secret'],
            ['./file'],
        ];
    }

    public function validPathsProvider()
    {
        return [
            ['file'],
            ['folder/file'],
            ['/file'],
            ['/folder/file'],
            ['/.file'],
            ['/..file'],
            ['/...file'],
            ['file.ext'],
            ['file..ext'],
            ['file...ext'],
            ['one,two.ext'],
            ['one(two)[].ext'],
            ['one=(two)[].ext'],
            ['one_(two)[].ext'],
            /*
            Example of a unicode-based filename with a single quote
            @see: https://github.com/octobercms/october/pull/4564
            */
            ['BG中国通讯期刊(Blend\'r)创刊号.pdf'],
        ];
    }

    /**
     * @dataProvider invalidPathsProvider
     */
    public function testInvalidPathsOnValidatePath($path)
    {
        $this->expectException('ApplicationException');
        MediaLibrary::validatePath($path);
    }

    /**
     * @dataProvider validPathsProvider
     */
    public function testValidPathsOnValidatePath($path)
    {
        $result = MediaLibrary::validatePath($path);
        $this->assertIsString($result);
    }

    public function testListFolderContents()
    {
        $this->setUpStorage();
        $this->copyMedia();

        // Rescan library
        MediaLibrary::instance()->scan(null, null, true);

        $contents = MediaLibrary::instance()->listFolderContents();

        $this->assertNotEmpty($contents, 'Media library item is not discovered');
        $this->assertCount(3, $contents);

        $this->assertEquals('file', $contents[0]->type, 'Media library item does not have the right type');
        $this->assertEquals('/text.txt', $contents[0]->path, 'Media library item does not have the right path');
        $this->assertNotEmpty($contents[0]->lastModified, 'Media library item last modified is empty');
        $this->assertNotEmpty($contents[0]->size, 'Media library item size is empty');

        $this->assertEquals('file', $contents[1]->type, 'Media library item does not have the right type');
        $this->assertEquals('/winter.png', $contents[1]->path, 'Media library item does not have the right path');
        $this->assertNotEmpty($contents[1]->lastModified, 'Media library item last modified is empty');
        $this->assertNotEmpty($contents[1]->size, 'Media library item size is empty');

        $this->assertEquals('file', $contents[2]->type, 'Media library item does not have the right type');
        $this->assertEquals('/winter space.png', $contents[2]->path, 'Media library item does not have the right path');
        $this->assertNotEmpty($contents[2]->lastModified, 'Media library item last modified is empty');
        $this->assertNotEmpty($contents[2]->size, 'Media library item size is empty');
    }

    public function testListAllDirectories()
    {
        $disk = $this->createMock(FilesystemInterface::class);

        $disk->expects($this->any())
            ->method('listContents')
            ->willReturnCallback(function ($path) {
                switch ($path) {
                    case '/media/':
                        return [
                            [
                                'type' => 'dir',
                                'path' => 'media/.ignore1',
                                'timestamp' => now(),
                                'size' => 0,
                                'dirname' => 'media',
                                'basename' => '.ignore1',
                                'extension' => '',
                                'filename' => '.ignore1'
                            ],
                            [
                                'type' => 'dir',
                                'path' => 'media/.ignore2',
                                'timestamp' => now(),
                                'size' => 0,
                                'dirname' => 'media',
                                'basename' => '.ignore2',
                                'extension' => '',
                                'filename' => '.ignore2'
                            ],
                            [
                                'type' => 'dir',
                                'path' => 'media/dir',
                                'timestamp' => now(),
                                'size' => 0,
                                'dirname' => 'media',
                                'basename' => 'dir',
                                'extension' => '',
                                'filename' => 'dir'
                            ],
                            [
                                'type' => 'dir',
                                'path' => 'media/exclude',
                                'timestamp' => now(),
                                'size' => 0,
                                'dirname' => 'media',
                                'basename' => 'exclude',
                                'extension' => '',
                                'filename' => 'exclude'
                            ],
                            [
                                'type' => 'dir',
                                'path' => 'media/hidden',
                                'timestamp' => now(),
                                'size' => 0,
                                'dirname' => 'media',
                                'basename' => 'hidden',
                                'extension' => '',
                                'filename' => 'hidden'
                            ],
                            [
                                'type' => 'dir',
                                'path' => 'media/hidden but not really',
                                'timestamp' => now(),
                                'size' => 0,
                                'dirname' => 'media',
                                'basename' => 'hidden but not really',
                                'extension' => '',
                                'filename' => 'hidden but not really'
                            ],
                            [
                                'type' => 'file',
                                'path' => 'media/name',
                                'timestamp' => now(),
                                'size' => 24,
                                'dirname' => 'media',
                                'basename' => 'name',
                                'extension' => '',
                                'filename' => 'name'
                            ],
                        ];
                        break;
                    case '/media/dir':
                        return [
                            [
                                'type' => 'dir',
                                'path' => 'media/dir/sub',
                                'timestamp' => now(),
                                'size' => 0,
                                'dirname' => 'media/dir',
                                'basename' => 'sub',
                                'extension' => '',
                                'filename' => 'sub'
                            ],
                        ];
                        break;
                    case '/media/hidden':
                        return [
                            [
                                'type' => 'dir',
                                'path' => 'media/hidden/sub1',
                                'timestamp' => now(),
                                'size' => 0,
                                'dirname' => 'media/hidden',
                                'basename' => 'sub1',
                                'extension' => '',
                                'filename' => 'sub1'
                            ],
                            [
                                'type' => 'dir',
                                'path' => 'media/hidden/sub2',
                                'timestamp' => now(),
                                'size' => 0,
                                'dirname' => 'media/hidden',
                                'basename' => 'sub2',
                                'extension' => '',
                                'filename' => 'sub2'
                            ],
                        ];
                        break;
                    case '/media/hidden/sub1':
                        return [
                            [
                                'type' => 'dir',
                                'path' => 'media/hidden/sub1/deep1',
                                'timestamp' => now(),
                                'size' => 0,
                                'dirname' => 'media/hidden/sub1',
                                'basename' => 'deep1',
                                'extension' => '',
                                'filename' => 'deep1'
                            ],
                        ];
                        break;
                    default:
                        return [];
                }
            });

        $this->app['config']->set('cms.storage.media.folder', 'media');
        $this->app['config']->set('cms.storage.media.ignore', ['hidden']);
        $this->app['config']->set('cms.storage.media.ignorePatterns', ['^\..*']);
        $instance = MediaLibrary::instance();
        $this->setProtectedProperty($instance, 'storageDisk', $disk);

        // Rescan library
        MediaLibrary::instance()->scan(null, null, true);

        $this->assertEquals(['/', '/dir', '/dir/sub', '/hidden but not really'], $instance->listAllDirectories(['/exclude']));
    }

    protected function setUpStorage()
    {
        $this->app->useStoragePath(base_path('storage/temp'));

        config(['filesystems.disks.test_local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),
        ]]);

        config(['cms.storage.media' => [
            'disk'   => 'test_local',
            'folder' => 'media',
            'path'   => '/storage/app/media',
        ]]);
    }

    protected function copyMedia()
    {
        $mediaPath = storage_path('app/media');

        if (!is_dir($mediaPath)) {
            mkdir($mediaPath, 0777, true);
        }

        foreach (glob(base_path('modules/system/tests/fixtures/media/*')) as $file) {
            $path = pathinfo($file);
            copy($file, $mediaPath . DIRECTORY_SEPARATOR . $path['basename']);
        }
    }

    protected function removeMedia()
    {
        if ($this->app->storagePath() !== base_path('storage/temp')) {
            return;
        }

        foreach (glob(storage_path('app/media/*')) as $file) {
            unlink($file);
        }

        rmdir(storage_path('app/media'));
        rmdir(storage_path('app'));
    }
}
