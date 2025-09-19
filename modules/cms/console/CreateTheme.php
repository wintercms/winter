<?php namespace Cms\Console;

use InvalidArgumentException;
use System\Classes\Asset\PackageManager;
use Winter\Storm\Exception\SystemException;
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
        {scaffold? : The base theme scaffold to use <info>(eg: less, tailwind)</info>}
        {--f|force : Overwrite existing files with generated files.}
        {--uninspiring : Disable inspirational quotes}
    ';

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
     * @var string The scaffold that we are building
     */
    protected string $scaffold;

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
            'scaffold/theme/tailwind/README.stub' => 'README.md',
            'scaffold/theme/tailwind/theme.stub' => 'theme.yaml',
            'scaffold/theme/tailwind/version.stub' => 'version.yaml',
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
        $this->scaffold = $this->argument('scaffold') ?? 'tailwind';

        $validOptions = $this->suggestScaffoldValues();
        if (!in_array($this->scaffold, $validOptions)) {
            throw new InvalidArgumentException("$this->scaffold is not an available theme scaffold type (Available types: " . implode(', ', $validOptions) . ')');
        }
        $this->stubs = $this->themeScaffolds[$this->scaffold];

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

    public function makeStubs(): void
    {
        parent::makeStubs();

        if ($this->scaffold === 'tailwind') {
            // @TODO: allow support for mix here
            $this->tailwindPostCreate('vite');
        }
    }

    protected function tailwindPostCreate(string $processor): void
    {
        if ($this->call('npm:version', ['--silent' => true, '--compatible' => true]) !== 0) {
            throw new SystemException(sprintf(
                'NPM is not installed or is outdated, please ensure NPM >= v7.0 is available and then manually set up %s.',
                $processor
            ));
        }

        $commands = [
            // Set up the vite config files
            $processor . ':create' => [
                'message' => 'Generating ' . $processor . ' + tailwind config...',
                'args' => [
                    'packageName' => 'theme-' . $this->getNameInput(),
                    '--no-interaction' => true,
                    '--force' => true,
                    '--silent' => true,
                    '--tailwind' => true
                ]
            ],
            // Ensure all require packages are available for the new theme and add the new theme to our npm workspaces
            $processor . ':install' => [
                'message' => 'Installing NPM dependencies...',
                'args' => [
                    'assetPackage' => ['theme-' . $this->getNameInput()],
                    '--no-interaction' => true,
                    '--silent' => false,
                    '--disable-tty' => true
                ]
            ],
            // Run an initial compile to ensure styles are available for first load
            $processor . ':compile' => [
                'message' => 'Compiling your theme...',
                'args' => [
                    '--package' => ['theme-' . $this->getNameInput()],
                    '--no-interaction' => true,
                    '--silent' => true,
                ]
            ]
        ];

        foreach ($commands as $command => $data) {
            $this->info($data['message']);

            // Handle commands throwing errors
            if ($this->call($command, $data['args']) !== 0) {
                throw new SystemException(sprintf('Post create command `%s` failed, please review manually.', $command));
            }

            // Force PackageManger to reset available packages
            if ($command === $processor . ':create') {
                PackageManager::forgetInstance();
            }
        }
    }
}
