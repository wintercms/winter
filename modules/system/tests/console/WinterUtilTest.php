<?php

namespace System\Tests\Console;

use System\Tests\Bootstrap\TestCase;

class WinterUtilTest extends TestCase
{
    public function testCompileLang()
    {
        $defaultClient = base_path('/modules/system/lang/en/client.php');

        // mv file so we can inject a test
        rename($defaultClient, $defaultClient . '.backup');

        file_put_contents($defaultClient, '<?php return [\'winter\' => \'is coming\'];');

        // execute compile
        $this->artisan('winter:util compile lang')->execute();

        // validate default lang handling
        $lang = file_get_contents(base_path('modules/system/assets/js/lang/lang.en.js'));
        $this->assertStringContainsString('winter', $lang);
        $this->assertStringContainsString('is coming', $lang);

        // simulate override
        $created = [];

        foreach (['lang/en/system', 'lang/en-gb/system'] as $slug) {
            $path = rtrim(base_path(), '/');
            foreach (explode('/', $slug) as $dir) {
                $path = $path . '/' . $dir;
                if (!is_dir($path)) {
                    mkdir($path, 0755);
                    $created[] = $path;
                }
            }
        }

        $langClient = base_path('lang/en/system/client.php');
        // handle existing file
        if (file_exists($langClient)) {
            rename($langClient, $langClient . '.backup');
        }

        file_put_contents($langClient, '<?php return [\'winter\' => \'is epic\'];');

        $langCountryClient = base_path('lang/en-gb/system/client.php');
        // handle existing file
        if (file_exists($langCountryClient)) {
            rename($langCountryClient, $langCountryClient . '.backup');
        }

        file_put_contents($langCountryClient, '<?php return [\'whats_epic\' => \'winter\'];');

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

        // restore
        unlink($defaultClient);
        rename($defaultClient . '.backup', $defaultClient);

        foreach ([$langClient, $langCountryClient] as $client) {
            unlink($client);
            if (file_exists($client . '.backup')) {
                rename($client . '.backup', $client);
            }
        }

        foreach (array_reverse($created) as $dir) {
            rmdir($dir);
        }

        // regenerate original compiled lang
        $this->artisan('winter:util compile lang')->execute();
    }
}
