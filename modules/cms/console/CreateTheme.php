<?php namespace Cms\Console;

use Exception;
use Winter\Storm\Scaffold\GeneratorCommand;

class CreateTheme extends GeneratorCommand
{
    /**
     * The default command name for lazy loading.
     *
     * @var string|null
     */
    protected static $defaultName = 'create:theme';

    /**
     * The name and signature of this command.
     *
     * @var string
     */
    protected $signature = 'create:theme
        {theme : The name of the theme to create. <info>(eg: MyTheme)</info>}
        {--force : Overwrite existing files with generated files.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new theme.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Theme';

    /**
     * A mapping of stub to generated file.
     *
     * @var array
     */
    protected $stubs = [
        'scaffold/theme/assets/js/app.stub' => 'assets/js/app.js',
        'scaffold/theme/assets/less/theme.stub' => 'assets/less/theme.less',
        'scaffold/theme/layouts/default.stub' => 'layouts/default.htm',
        'scaffold/theme/pages/404.stub' => 'pages/404.htm',
        'scaffold/theme/pages/error.stub' => 'pages/error.htm',
        'scaffold/theme/pages/home.stub' => 'pages/home.htm',
        'scaffold/theme/partials/meta/seo.stub' => 'partials/meta/seo.htm',
        'scaffold/theme/partials/meta/styles.stub' => 'partials/meta/styles.htm',
        'scaffold/theme/partials/site/header.stub' => 'partials/site/header.htm',
        'scaffold/theme/partials/site/footer.stub' => 'partials/site/footer.htm',
        'scaffold/theme/theme.stub' => 'theme.yaml',
        'scaffold/theme/version.stub' => 'version.yaml',
    ];

    /**
     * Prepare variables for stubs.
     *
     * return @array
     */
    protected function prepareVars()
    {
        /*
         * Extract the author and name from the plugin code
         */
        $code = str_slug($this->argument('theme'));

        return [
            'code' => $code,
        ];
    }

    /**
     * Get the plugin path from the input.
     *
     * @return string
     */
    protected function getDestinationPath()
    {
        $code = $this->prepareVars()['code'];

        return themes_path($code);
    }

    /**
     * Make a single stub.
     *
     * @param string $stubName The source filename for the stub.
     */
    public function makeStub($stubName)
    {
        if (!isset($this->stubs[$stubName])) {
            return;
        }

        $sourceFile = $this->getSourcePath() . '/' . $stubName;
        $destinationFile = $this->getDestinationPath() . '/' . $this->stubs[$stubName];
        $destinationContent = $this->files->get($sourceFile);

        /*
         * Parse each variable in to the destination content and path
         */
        foreach ($this->vars as $key => $var) {
            $destinationContent = str_replace('{{' . $key . '}}', $var, $destinationContent);
            $destinationFile = str_replace('{{' . $key . '}}', $var, $destinationFile);
        }

        $this->makeDirectory($destinationFile);

        /*
         * Make sure this file does not already exist
         */
        if ($this->files->exists($destinationFile) && !$this->option('force')) {
            throw new Exception('Stop everything!!! This file already exists: ' . $destinationFile);
        }

        $this->files->put($destinationFile, $destinationContent);
    }
}
