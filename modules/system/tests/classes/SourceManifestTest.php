<?php

namespace System\Tests\Classes;

use System\Tests\Bootstrap\TestCase;
use Winter\Storm\Argon\Argon;
use System\Classes\SourceManifest;
use System\Classes\FileManifest;

class SourceManifestTest extends TestCase
{
    /** @var SourceManifest instance */
    protected $sourceManifest;

    /** @var array Emulated builds from the manifest fixture */
    protected $builds;

    public function setUp(): void
    {
        parent::setUp();

        $this->builds = [
            '1.0.0' => new FileManifest(
                base_path('modules/system/tests/fixtures/manifest/1_0_0'),
                ['test', 'test2']
            ),
            '1.0.1' => new FileManifest(
                base_path('modules/system/tests/fixtures/manifest/1_0_1'),
                ['test', 'test2']
            ),
            '1.0.2' => new FileManifest(
                base_path('modules/system/tests/fixtures/manifest/1_0_2'),
                ['test', 'test2']
            ),
            '1.1.0' => new FileManifest(
                base_path('modules/system/tests/fixtures/manifest/1_1_0'),
                ['test', 'test2', 'test3']
            ),
            '1.1.1' => new FileManifest(
                base_path('modules/system/tests/fixtures/manifest/1_1_1'),
                ['test', 'test2', 'test3']
            ),
            '1.0.3' => new FileManifest(
                base_path('modules/system/tests/fixtures/manifest/1_0_3'),
                ['test', 'test2']
            ),
        ];

        $this->sourceManifest = new SourceManifest($this->manifestPath(), $this->forksPath(), false);
    }

    public function tearDown(): void
    {
        Argon::setTestNow();
        $this->deleteManifest();
    }

    public function testCreateManifest()
    {
        // Freeze date for test
        $testDate = Argon::create(2020, 12, 16, 12, 01, 0, 'UTC');
        Argon::setTestNow($testDate);

        $this->createManifest(true);

        $this->assertEquals(
            '{' . "\n" .
            '    "_description": "This is the source manifest of changes to Winter CMS for each version. This is used to determine which version of Winter CMS is in use, via the \"winter:version\" Artisan command.",' . "\n" .
            '    "_created": "2020-12-16T12:01:00+00:00",' . "\n" .
            '    "manifest": [' . "\n" .
            '        {' . "\n" .
            '            "build": "1.0.0",' . "\n" .
            '            "parent": null,' . "\n" .
            '            "modules": {' . "\n" .
            '                "test": "e1d6c6e4c482688e231ee37d89668268426512013695de47bfcb424f9a645c7b",' . "\n" .
            '                "test2": "a7ffc6f8bf1ed76651c14756a061d662f580ff4de43b49fa82d80a4b80f8434a"' . "\n" .
            '            },' . "\n" .
            '            "files": {' . "\n" .
            '                "added": {' . "\n" .
            '                    "\/modules\/test\/file1.php": "6f9b0b94528a85b2a6bb67b5621e074aef1b4c9fc9ee3ea1bd69100ea14cb3db"' . "\n" .
            '                }' . "\n" .
            '            }' . "\n" .
            '        },' . "\n" .
            '        {' . "\n" .
            '            "build": "1.0.1",' . "\n" .
            '            "parent": "1.0.0",' . "\n" .
            '            "modules": {' . "\n" .
            '                "test": "c0b794ff210862a4ce16223802efe6e28969f5a4fb42480ec8c2fef2da23d181",' . "\n" .
            '                "test2": "32c9f2fb6e0a22dde288a0fe1e4834798360b25e5a91d2597409d9302221381d"' . "\n" .
            '            },' . "\n" .
            '            "files": {' . "\n" .
            '                "added": {' . "\n" .
            '                    "\/modules\/test\/file2.php": "96ae9f6b6377ad29226ea169f952de49fc29ae895f18a2caed76aeabdf050f1b",' . "\n" .
            '                    "\/modules\/test2\/file1.php": "94bd47b1ac7b2837b31883ebcd38c8101687321f497c3c4b9744f68ae846721d"' . "\n" .
            '                }' . "\n" .
            '            }' . "\n" .
            '        },' . "\n" .
            '        {' . "\n" .
            '            "build": "1.0.2",' . "\n" .
            '            "parent": "1.0.1",' . "\n" .
            '            "modules": {' . "\n" .
            '                "test": "419a3c073a4296213cdc9319cfc488383753e2e81cefa1c73db38749b82a3c51",' . "\n" .
            '                "test2": "32c9f2fb6e0a22dde288a0fe1e4834798360b25e5a91d2597409d9302221381d"' . "\n" .
            '            },' . "\n" .
            '            "files": {' . "\n" .
            '                "added": {' . "\n" .
            '                    "\/modules\/test\/file3.php": "7f4132b05911a6b0df4d41bf5dc3d007786b63a5a22daf3060ed222816d57b54"' . "\n" .
            '                },' . "\n" .
            '                "modified": {' . "\n" .
            '                    "\/modules\/test\/file2.php": "2c61b2f5688275574251a19a57e06a4eb9e537b3916ebf6f71768e184a4ae538"' . "\n" .
            '                },' . "\n" .
            '                "removed": [' . "\n" .
            '                    "\/modules\/test\/file1.php"' . "\n" .
            '                ]' . "\n" .
            '            }' . "\n" .
            '        },' . "\n" .
            '        {' . "\n" .
            '            "build": "1.0.3",' . "\n" .
            '            "parent": "1.0.2",' . "\n" .
            '            "modules": {' . "\n" .
            '                "test": "5316f172ac24aaa7713c97885e2e27f5c5c1e02f96fcb8e269903d737d52c7bd",' . "\n" .
            '                "test2": "18fc53cb280cc7e43d47e55a66b1245992c331f368106c13024572dc9fb215f7"' . "\n" .
            '            },' . "\n" .
            '            "files": {' . "\n" .
            '                "modified": {' . "\n" .
            '                    "\/modules\/test2\/file1.php": "e284e816365653b5f7ddfca9319c24716cadfa3538e0c79994f0416e964da513"' . "\n" .
            '                },' . "\n" .
            '                "removed": [' . "\n" .
            '                    "\/modules\/test\/file3.php"' . "\n" .
            '                ]' . "\n" .
            '            }' . "\n" .
            '        },' . "\n" .
            '        {' . "\n" .
            '            "build": "1.1.0",' . "\n" .
            '            "parent": "1.0.2",' . "\n" .
            '            "modules": {' . "\n" .
            '                "test": "e30811c9ad3119394edefd2f2fc0bae5fc08e2a03220ccc94e2a3f078da0df6d",' . "\n" .
            '                "test2": "2066b3cffe4f03b06c3399dba4d56147e4247b8071e19fa597a2e15dec697f5e",' . "\n" .
            '                "test3": "3679fa86c8d92a4474a01fc41abc24ebf34b847d7288434028a0559a10ff5d33"' . "\n" .
            '            },' . "\n" .
            '            "files": {' . "\n" .
            '                "added": {' . "\n" .
            '                    "\/modules\/test\/file4.php": "18047babf2625ed8f42d779f14539449e23ccfdc92f79818f291eb1c55ff0533",' . "\n" .
            '                    "\/modules\/test3\/file1.php": "1b691b48b8247af3dd8046a70e5678b77e66ada13b86c3a269a6428425c3a835"' . "\n" .
            '                },' . "\n" .
            '                "modified": {' . "\n" .
            '                    "\/modules\/test\/file3.php": "65b1d6f8a1a0f2dfbe870faba9fa2ee0b6f4c291656df6cd66158e166a2680eb",' . "\n" .
            '                    "\/modules\/test2\/file1.php": "5197bcc06443799c71fea8cb608cee61d9edd611ea23ea603aa0420393e014ca"' . "\n" .
            '                },' . "\n" .
            '                "removed": [' . "\n" .
            '                    "\/modules\/test\/file2.php"' . "\n" .
            '                ]' . "\n" .
            '            }' . "\n" .
            '        },' . "\n" .
            '        {' . "\n" .
            '            "build": "1.1.1",' . "\n" .
            '            "parent": "1.1.0",' . "\n" .
            '            "modules": {' . "\n" .
            '                "test": "e30811c9ad3119394edefd2f2fc0bae5fc08e2a03220ccc94e2a3f078da0df6d",' . "\n" .
            '                "test2": "18fc53cb280cc7e43d47e55a66b1245992c331f368106c13024572dc9fb215f7",' . "\n" .
            '                "test3": "728ff099502e8d40cc6a432fdeba7185b9cb5cb530dcd8f5741ec56fcd37d189"' . "\n" .
            '            },' . "\n" .
            '            "files": {' . "\n" .
            '                "added": {' . "\n" .
            '                    "\/modules\/test3\/file2.php": "0614afe438a4e74547a7036ef895008c36895a783eb8e0b458691f631138d310"' . "\n" .
            '                },' . "\n" .
            '                "modified": {' . "\n" .
            '                    "\/modules\/test2\/file1.php": "e284e816365653b5f7ddfca9319c24716cadfa3538e0c79994f0416e964da513",' . "\n" .
            '                    "\/modules\/test3\/file1.php": "d9aeb0853bc0f21a836f2d1cd5fc224d64ce8ea9e7e22c43b1a00f25d37c42f8"' . "\n" .
            '                }' . "\n" .
            '            }' . "\n" .
            '        }' . "\n" .
            '    ]' . "\n" .
            '}',
            file_get_contents($this->manifestPath())
        );
    }

    public function testGetBuilds()
    {
        $this->createManifest();

        $buildKeys = $this->sourceManifest->getBuilds();

        $this->assertCount(6, $buildKeys);
        $this->assertEquals(['1.0.0', '1.0.1', '1.0.2', '1.0.3', '1.1.0', '1.1.1'], $buildKeys);
    }

    public function testGetState()
    {
        $this->createManifest();

        $this->assertEquals([
            '/modules/test/file1.php' => '6f9b0b94528a85b2a6bb67b5621e074aef1b4c9fc9ee3ea1bd69100ea14cb3db',
        ], $this->sourceManifest->getState('1.0.0'));

        $this->assertEquals([
            '/modules/test/file1.php' => '6f9b0b94528a85b2a6bb67b5621e074aef1b4c9fc9ee3ea1bd69100ea14cb3db',
            '/modules/test/file2.php' => '96ae9f6b6377ad29226ea169f952de49fc29ae895f18a2caed76aeabdf050f1b',
            '/modules/test2/file1.php' => '94bd47b1ac7b2837b31883ebcd38c8101687321f497c3c4b9744f68ae846721d',
        ], $this->sourceManifest->getState('1.0.1'));

        $this->assertEquals([
            '/modules/test/file2.php' => '2c61b2f5688275574251a19a57e06a4eb9e537b3916ebf6f71768e184a4ae538',
            '/modules/test/file3.php' => '7f4132b05911a6b0df4d41bf5dc3d007786b63a5a22daf3060ed222816d57b54',
            '/modules/test2/file1.php' => '94bd47b1ac7b2837b31883ebcd38c8101687321f497c3c4b9744f68ae846721d',
        ], $this->sourceManifest->getState('1.0.2'));

        $this->assertEquals([
            '/modules/test/file2.php' => '2c61b2f5688275574251a19a57e06a4eb9e537b3916ebf6f71768e184a4ae538',
            '/modules/test2/file1.php' => 'e284e816365653b5f7ddfca9319c24716cadfa3538e0c79994f0416e964da513',
        ], $this->sourceManifest->getState('1.0.3'));

        $this->assertEquals([
            '/modules/test/file3.php' => '65b1d6f8a1a0f2dfbe870faba9fa2ee0b6f4c291656df6cd66158e166a2680eb',
            '/modules/test/file4.php' => '18047babf2625ed8f42d779f14539449e23ccfdc92f79818f291eb1c55ff0533',
            '/modules/test2/file1.php' => '5197bcc06443799c71fea8cb608cee61d9edd611ea23ea603aa0420393e014ca',
            '/modules/test3/file1.php' => '1b691b48b8247af3dd8046a70e5678b77e66ada13b86c3a269a6428425c3a835',
        ], $this->sourceManifest->getState('1.1.0'));

        $this->assertEquals([
            '/modules/test/file3.php' => '65b1d6f8a1a0f2dfbe870faba9fa2ee0b6f4c291656df6cd66158e166a2680eb',
            '/modules/test/file4.php' => '18047babf2625ed8f42d779f14539449e23ccfdc92f79818f291eb1c55ff0533',
            '/modules/test2/file1.php' => 'e284e816365653b5f7ddfca9319c24716cadfa3538e0c79994f0416e964da513',
            '/modules/test3/file1.php' => 'd9aeb0853bc0f21a836f2d1cd5fc224d64ce8ea9e7e22c43b1a00f25d37c42f8',
            '/modules/test3/file2.php' => '0614afe438a4e74547a7036ef895008c36895a783eb8e0b458691f631138d310',
        ], $this->sourceManifest->getState('1.1.1'));
    }

    public function testCompare()
    {
        $this->createManifest();

        $this->assertEquals([
            'build' => '1.0.0',
            'modified' => false,
            'confident' => true
        ], $this->sourceManifest->compare($this->builds['1.0.0']));

        $this->assertEquals([
            'build' => '1.0.1',
            'modified' => false,
            'confident' => true
        ], $this->sourceManifest->compare($this->builds['1.0.1']));

        $this->assertEquals([
            'build' => '1.0.2',
            'modified' => false,
            'confident' => true
        ], $this->sourceManifest->compare($this->builds['1.0.2']));

        $this->assertEquals([
            'build' => '1.0.3',
            'modified' => false,
            'confident' => true
        ], $this->sourceManifest->compare($this->builds['1.0.3']));

        $this->assertEquals([
            'build' => '1.1.0',
            'modified' => false,
            'confident' => true
        ], $this->sourceManifest->compare($this->builds['1.1.0']));

        $this->assertEquals([
            'build' => '1.1.1',
            'modified' => false,
            'confident' => true
        ], $this->sourceManifest->compare($this->builds['1.1.1']));
    }

    public function testCompareModified()
    {
        $this->createManifest();

        // Hot-swap "tests/fixtures/manifest/1_1_1/modules/test/file3.php"
        $old = file_get_contents(base_path('modules/system/tests/fixtures/manifest/1_1_1/modules/test/file3.php'));
        file_put_contents(base_path('modules/system/tests/fixtures/manifest/1_1_1/modules/test/file3.php'), '<?php // Changed');

        $modifiedManifest = new FileManifest(base_path('modules/system/tests/fixtures/manifest/1_1_1'), ['test', 'test2']);
        $modifiedManifest->getFiles();

        file_put_contents(base_path('modules/system/tests/fixtures/manifest/1_1_1/modules/test/file3.php'), $old);

        $this->assertEquals([
            'build' => '1.1.1',
            'modified' => true,
            'confident' => true,
        ], $this->sourceManifest->compare($modifiedManifest));
    }

    public function testCompareModifiedSecondBranch()
    {
        $this->createManifest();

        // Add "tests/fixtures/manifest/1_0_3/modules/test/file3.php"
        file_put_contents(base_path('modules/system/tests/fixtures/manifest/1_0_3/modules/test/file3.php'), '<?php // Changed');

        $modifiedManifest = new FileManifest(base_path('modules/system/tests/fixtures/manifest/1_0_3'), ['test', 'test2']);
        $modifiedManifest->getFiles();

        unlink(base_path('modules/system/tests/fixtures/manifest/1_0_3/modules/test/file3.php'));

        $this->assertEquals([
            'build' => '1.0.3',
            'modified' => true,
            'confident' => false, // 50% match, not confident
        ], $this->sourceManifest->compare($modifiedManifest));
    }

    protected function createManifest(bool $write = false)
    {
        $this->deleteManifest();

        foreach ($this->builds as $build => $fileManifest) {
            $this->sourceManifest->addBuild($build, $fileManifest);
        }

        if ($write) {
            file_put_contents($this->manifestPath(), $this->sourceManifest->generate());
        }

        $this->sourceManifest->loadForks();
    }

    protected function deleteManifest()
    {
        if (file_exists($this->manifestPath())) {
            unlink($this->manifestPath());
        }
    }

    protected function manifestPath()
    {
        return base_path('modules/system/tests/fixtures/manifest/builds.json');
    }

    protected function forksPath()
    {
        return base_path('modules/system/tests/fixtures/manifest/forks.json');
    }
}
