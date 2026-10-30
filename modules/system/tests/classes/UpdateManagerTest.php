<?php

namespace System\Tests\Classes;

use ApplicationException;
use System\Classes\UpdateManager;
use System\Models\Parameter;
use System\Tests\Bootstrap\PluginTestCase;

class UpdateManagerTest extends PluginTestCase
{
    protected UpdateManager $manager;

    public function setUp(): void
    {
        parent::setUp();

        $this->manager = UpdateManager::instance();
    }

    public function buildIdentifiers(): array
    {
        return [
            'semantic version' => ['1.2.14'],
            'pre-release version' => ['1.3.0-beta.2'],
            'legacy build number' => ['468'],
            'underscored identifier' => ['1_2_14'],
        ];
    }

    /**
     * @dataProvider buildIdentifiers
     */
    public function testSetBuildStoresAVersionIdentifier(string $build): void
    {
        $this->manager->setBuild($build, 'abc123');

        $this->assertSame($build, Parameter::get('system::core.build'));
        $this->assertSame('abc123', Parameter::get('system::core.hash'));
        $this->assertFalse(Parameter::get('system::core.modified'));
    }

    public function malformedBuildIdentifiers(): array
    {
        return [
            'quote' => ["1'"],
            'double quote' => ['1"'],
            'angle brackets' => ['1<b>'],
            'whitespace' => ['1 2'],
            'slash' => ['1/2'],
            'backslash' => ['1\\2'],
            'newline' => ["1\n2"],
            'empty' => [''],
        ];
    }

    /**
     * @dataProvider malformedBuildIdentifiers
     */
    public function testSetBuildRefusesAMalformedIdentifier(string $build): void
    {
        $this->manager->setBuild('1.2.14', 'abc123');

        try {
            $this->manager->setBuild($build, 'def456');
            $this->fail('setBuild() accepted ' . var_export($build, true));
        } catch (ApplicationException $ex) {
            // Expected.
        }

        // The previously stored values are left untouched.
        $this->assertSame('1.2.14', Parameter::get('system::core.build'));
        $this->assertSame('abc123', Parameter::get('system::core.hash'));
    }

    public function testSetBuildRefusesANonScalarIdentifier(): void
    {
        $this->expectException(ApplicationException::class);

        $this->manager->setBuild(['1.2.14']);
    }
}
