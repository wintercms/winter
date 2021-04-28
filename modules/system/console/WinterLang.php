<?php namespace System\Console;

use File;
use Yaml;
use Symfony\Component\Yaml\Dumper as YamlDumper;
use ApplicationException;
use InvalidArgumentException;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Console command to add missing language translation keys for a target locale
 *
 * @package winter\wn-system-module
 * @author Marc Jauvin
 * @author Winter CMS
 */
class WinterLang extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'winter:lang';

    /**
     * @var string The console command description.
     */
    protected $description = 'Add missing language translation keys for target locale';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = $this->argument('module')
            ?? $this->ask("Provide a module ['cms', 'backend', 'system']");

        $modulePath = base_path('modules/' . $module);
        if (!File::isDirectory($modulePath)) {
            throw new InvalidArgumentException('The module does not exists: ' . $modulePath);
        }

        $langFile = $this->argument('langFile')
            ?? $this->ask('Provide a language file');

        $langFile = basename($langFile, '.php') . '.php';

        $langFilePath = $modulePath . '/lang/en/' . $langFile;
        if (!File::exists($langFilePath)) {
            throw new InvalidArgumentException('The language file does not exist: ' . $langFilePath);
        }
        $strings = $result = include($langFilePath);

        $targetLocale = $this->argument('targetLocale')
            ?? $this->ask('For which language do you want to generate missing keys');

        $targetLangFilePath = $modulePath . '/lang/' . $targetLocale . '/' . $langFile;

        if (File::exists($targetLangFilePath)) {
            $targetStrings = include($targetLangFilePath);
            $result = array_merge($strings, $targetStrings);
        }

        File::put('php://stdout', $this->dumpStrings($result));

        exit(0);
    }

    protected function dumpStrings($stringsArray)
    {
        if (count($stringsArray) > 0) {
            $dumper = new YamlDumper();
            $strings = trim($dumper->dump($stringsArray, 20, 0, false, true));
        } else {
            $strings = '';
        }

        if (!strlen($strings)) {
            return "<?php return [\n];";
        }

        try {
            $data = $this->getSanitizedPHPStrings(Yaml::parse($strings));

            $phpData = var_export($data, true);
            $phpData = preg_replace('/^(\s+)\),/m', '$1],', $phpData);
            $phpData = preg_replace('/^(\s+)array\s+\(/m', '$1[', $phpData);
            $phpData = preg_replace_callback('/^(\s+)/m', function ($matches) {
                return str_repeat($matches[1], 2); // Increase indentation
            }, $phpData);
            $phpData = preg_replace('/\n\s+\[/m', '[', $phpData);
            $phpData = preg_replace('/^array\s\(/', '[', $phpData);
            $phpData = preg_replace('/^\)\Z/m', ']', $phpData);

            return "<?php return ".$phpData.";";
        }
        catch (Exception $ex) {
            throw new ApplicationException(sprintf('Cannot parse the YAML content: %s', $ex->getMessage()));
        }
    }

    protected function getSanitizedPHPStrings($strings)
    {
        array_walk_recursive($strings, function (&$item, $key) {
            if (!is_scalar($item)) {
                return;
            }

            // In YAML single quotes are escaped with two single quotes
            // http://yaml.org/spec/current.html#id2534365
            $item = str_replace("''", "'", $item);
        });

        return $strings;
    }

    /**
     * Get the console command options.
     */
    protected function getArguments()
    {
        return [
            ['module', InputArgument::OPTIONAL, 'The module for the language file'],
            ['langFile', InputArgument::OPTIONAL, 'The language file to work on'],
            ['targetLocale', InputArgument::OPTIONAL, 'The target locale for which to add missing keys'],
        ];
    }
}
