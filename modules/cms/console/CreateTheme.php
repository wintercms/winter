<?php namespace Cms\Console;

use Winter\Storm\Scaffold\GeneratorCommand;
use InvalidArgumentException;

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
        {scaffold? : The base theme scaffold to use <info>(eg: less, tailwind)</info>}
        {--f|force : Overwrite existing files with generated files.}';

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
     * @var array Available theme scaffolds and their types
     */
    protected $themeScaffolds = [
        'less' => [
            'scaffold/theme/less/assets/js/app.stub' => 'assets/js/app.js',
            'scaffold/theme/less/assets/less/theme.stub' => 'assets/less/theme.less',
            'scaffold/theme/less/layouts/default.stub' => 'layouts/default.htm',
            'scaffold/theme/less/pages/404.stub' => 'pages/404.htm',
            'scaffold/theme/less/pages/error.stub' => 'pages/error.htm',
            'scaffold/theme/less/pages/home.stub' => 'pages/home.htm',
            'scaffold/theme/less/partials/meta/seo.stub' => 'partials/meta/seo.htm',
            'scaffold/theme/less/partials/meta/styles.stub' => 'partials/meta/styles.htm',
            'scaffold/theme/less/partials/site/header.stub' => 'partials/site/header.htm',
            'scaffold/theme/less/partials/site/footer.stub' => 'partials/site/footer.htm',
            'scaffold/theme/less/theme.stub' => 'theme.yaml',
            'scaffold/theme/less/version.stub' => 'version.yaml',
        ],
        'tailwind' => [
            'scaffold/theme/tailwind/assets/src/css/base.stub' => 'assets/src/css/base.css',
            'scaffold/theme/tailwind/assets/src/css/custom.stub' => 'assets/src/css/custom.css',
            'scaffold/theme/tailwind/assets/src/css/theme.stub' => 'assets/src/css/theme.css',
            'scaffold/theme/tailwind/assets/src/js/theme.stub' => 'assets/src/js/theme.js',
            'scaffold/theme/tailwind/lang/en/lang.stub' => 'lang/en/lang.php',
            'scaffold/theme/tailwind/layouts/default.stub' => 'layouts/default.htm',
            'scaffold/theme/tailwind/pages/404.stub' => 'pages/404.htm',
            'scaffold/theme/tailwind/pages/error.stub' => 'pages/error.htm',
            'scaffold/theme/tailwind/pages/home.stub' => 'pages/home.htm',
            'scaffold/theme/tailwind/partials/meta/seo.stub' => 'partials/meta/seo.htm',
            'scaffold/theme/tailwind/partials/meta/styles.stub' => 'partials/meta/styles.htm',
            'scaffold/theme/tailwind/partials/site/header.stub' => 'partials/site/header.htm',
            'scaffold/theme/tailwind/partials/site/footer.stub' => 'partials/site/footer.htm',
            'scaffold/theme/tailwind/.gitignore.stub' => '.gitignore',
            'scaffold/theme/tailwind/package.stub' => 'package.json',
            'scaffold/theme/tailwind/readme.stub' => 'README.md',
            'scaffold/theme/tailwind/tailwind.config.stub' => 'tailwind.config.js',
            'scaffold/theme/tailwind/theme.stub' => 'theme.yaml',
            'scaffold/theme/tailwind/version.stub' => 'version.yaml',
            'scaffold/theme/tailwind/winter.mix.stub' => 'winter.mix.js',
        ],
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
        $scaffold = $this->argument('scaffold') ?? 'less';
        $validOptions = $this->suggestScaffoldValues();
        if (!in_array($scaffold, $validOptions)) {
            throw new InvalidArgumentException("$scaffold is not an available theme scaffold type (Available types: " . implode(', ', $validOptions) . ')');
        }
        $this->stubs = $this->themeScaffolds[$scaffold];

        return [
            'code' => $this->getNameInput(),
        ];
    }

    /**
     * Auto suggest valid theme scaffold values
     */
    public function suggestScaffoldValues(): array
    {
        return array_keys($this->themeScaffolds);
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
