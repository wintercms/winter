<?php

namespace System\Tests\Console;

use System\Tests\Bootstrap\TestCase;

class WinterUtilTest extends TestCase
{
    protected string $defaultClient;
    protected string $langClient;
    protected string $langCountryClient;
    protected array $createdDirs = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultClient = base_path('modules/system/lang/en/client.php');
        $this->langClient = base_path('lang/en/system/client.php');
        $this->langCountryClient = base_path('lang/en-gb/system/client.php');

        // Backup original files
        if (file_exists($this->defaultClient)) {
            rename($this->defaultClient, $this->defaultClient . '.backup');
        }
        if (file_exists($this->langClient)) {
            rename($this->langClient, $this->langClient . '.backup');
        }
        if (file_exists($this->langCountryClient)) {
            rename($this->langCountryClient, $this->langCountryClient . '.backup');
        }
    }

    public function testCompileLang()
    {
        file_put_contents($this->defaultClient, '<?php return [\'winter\' => \'is coming\'];');

        // execute compile
        $this->artisan('winter:util compile lang')->execute();

        // validate default lang handling
        $lang = file_get_contents(base_path('modules/system/assets/js/lang/lang.en.js'));
        $this->assertStringContainsString('winter', $lang);
        $this->assertStringContainsString('is coming', $lang);

        // simulate override
        $this->createdDirs = [];

        foreach (['lang/en/system', 'lang/en-gb/system'] as $slug) {
            $path = rtrim(base_path(), '/');
            foreach (explode('/', $slug) as $dir) {
                $path = $path . '/' . $dir;
                if (!is_dir($path)) {
                    mkdir($path, 0755);
                    $this->createdDirs[] = $path;
                }
            }
        }

        file_put_contents($this->langClient, '<?php return [\'winter\' => \'is epic\'];');

        file_put_contents($this->langCountryClient, '<?php return [\'whats_epic\' => \'winter\'];');

        // execute compile
        $this->artisan('winter:util compile lang')->execute();

        // validate override handling
        $lang = file_get_contents(base_path('modules/system/assets/js/lang/lang.en.js'));
        $this->assertStringContainsString('winter', $lang);
        $this->assertStringContainsString('is epic', $lang);

        // check that lang subset has included parent overrides
        $lang = file_get_contents(base_path('modules/system/assets/js/lang/lang.en-gb.js'));
        $this->assertStringContainsString('winter', $lang);
        $this->assertStringContainsString('is epic', $lang);
        $this->assertStringContainsString('whats_epic', $lang);
        $this->assertStringContainsString('winter', $lang);
    }

    protected function tearDown(): void
    {
        unlink($this->defaultClient);
        rename($this->defaultClient . '.backup', $this->defaultClient);

        foreach ([$this->langClient, $this->langCountryClient] as $client) {
            unlink($client);
            if (file_exists($client . '.backup')) {
                rename($client . '.backup', $client);
            }
        }

        foreach (array_reverse($this->createdDirs) as $dir) {
            rmdir($dir);
        }

        $this->artisan('winter:util compile lang')->execute();
    }
}
