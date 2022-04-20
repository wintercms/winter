<?php namespace Cms\Console;

use Winter\Storm\Scaffold\GeneratorCommand;

class CreateTheme extends GeneratorCommand
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'create:theme';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'create:theme
        {theme : The name of the theme to create. <info>(eg: MyTheme)</info>}
        {--force : Overwrite existing files with generated files.}';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a new theme.';

    /**
     * @var string The type of class being generated.
     */
    protected $type = 'Theme';

    /**
     * @var string The argument that the generated class name comes from
     */
    protected $nameFrom = 'theme';

    /**
     * @var array A mapping of stub to generated file.
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
     * Get the desired class name from the input.
     */
    protected function getNameInput(): string
    {
        return str_slug(parent::getNameInput());
    }

    /**
     * Prepare variables for stubs.
     */
    protected function prepareVars(): array
    {
        return [
            'code' => $this->getNameInput(),
        ];
    }

    /**
     * Get the plugin path from the input.
     */
    protected function getDestinationPath(): string
    {
        return themes_path($this->getNameInput());
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
        $destinationFile = $this->getDestinationForStub($stubName);
        $destinationContent = $this->files->get($sourceFile);

        /*
         * Parse each variable in to the destination content and path
         * @NOTE: CANNOT USE TWIG AS IT WOULD CONFLICT WITH THE TWIG TEMPLATES THEMSELVES
         */
        foreach ($this->vars as $key => $var) {
            $destinationContent = str_replace('{{' . $key . '}}', $var, $destinationContent);
            $destinationFile = str_replace('{{' . $key . '}}', $var, $destinationFile);
        }

        $this->makeDirectory($destinationFile);

        $this->files->put($destinationFile, $destinationContent);
    }
}
