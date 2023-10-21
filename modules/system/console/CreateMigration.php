<?php namespace System\Console;

use File;
use InvalidArgumentException;
use System\Classes\VersionManager;
use System\Console\BaseScaffoldCommand;
use Winter\Storm\Database\Model;
use Winter\Storm\Support\Str;
use Yaml;

/**
 * Scaffolds a new migration file
 *
 * @TODO:
 * - Add flag to either create a new version automatically in version.yaml or
 *   add the migration to a specific version, would also put the migration in
 *   a version specific folder
 */
class CreateMigration extends BaseScaffoldCommand
{
    /**
     * @var string|null The default command name for lazy loading.
     */
    protected static $defaultName = 'create:migration';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'create:migration
        {plugin : The name of the plugin. <info>(eg: Winter.Blog)</info>}
        {--name= : The name of the migration}
        {--model= : The model to create a migration for. <info>(eg: Post)</info>}
        {--table= : The table to migrate, defaults to autogenerated from the provided model. <info>(eg: winter_blog_posts)</info>}
        {--for-version= : Generate a migration for a specific version}
        {--c|create : Generate a migration that creates the specified table}
        {--u|update : Generate a migration that updates the specified table}
        {--f|force : Overwrite existing files with generated files.}
        {--uninspiring : Disable inspirational quotes}
    ';

    /**
     * @var string The console command description.
     */
    protected $description = 'Creates a new migration.';

    /**
     * @var array List of commands that this command replaces (aliases)
     */
    protected $replaces = [
        'make:migration',
    ];

    /**
     * @var string The type of class being generated.
     */
    protected $type = 'Migration';

    /**
     * @var bool Validate the provided plugin input against the PluginManager.
     */
    protected $validatePluginInput = true;

    /**
     * @var array Available migration scaffolds and their types
     */
    protected $migrationScaffolds = [
        'create' => [
            'scaffold/migration/migration.create.stub' => 'updates/{{version}}/{{snake_name}}.php',
        ],
        'update' => [
            'scaffold/migration/migration.update.stub' => 'updates/{{version}}/{{snake_name}}.php',
        ],
        'migrate' => [
            'scaffold/migration/migration.stub' => 'updates/{{version}}/{{snake_name}}.php',
        ],
    ];

    /**
     * Make all stubs.
     */
    public function makeStubs(): void
    {
        parent::makeStubs();

        $plugin = $this->getPlugin();

        // Identify the changes to insert into the version.yaml file
        $changes = [$this->vars['name']];
        $stubs = array_keys($this->stubs);
        foreach ($stubs as $stub) {
            $changes[] = Str::after($this->getDestinationForStub($stub), $plugin->getPluginPath() . '/updates/');
        }

        // Identify the version to insert these changes into
        $versions = $plugin->getPluginVersions();
        $firstVersion = array_keys($versions)[0];
        $usesV = Str::startsWith($firstVersion, 'v');
        $version = $usesV ? $this->vars['version'] : Str::after($this->vars['version'], 'v');

        // Insert these changes into the identified version
        $changes = array_merge($versions[$version] ?? [], $changes);
        $versions[$version] = $changes;

        // Render and save the updated version.yaml file
        $destinationFile = $plugin->getPluginPath() . '/updates/version.yaml';
        $this->files->put($destinationFile, Yaml::render($versions));
        $this->comment('File updated: ' . str_replace(base_path(), '', $destinationFile));
    }

    /**
     * Get the desired class name from the input.
     */
    protected function getNameInput(): string
    {
        $name = trim($this->option($this->nameFrom));
        if (empty($name)) {
            if ($this->option('create')) {
                $template = 'Create {ResourceName} Table';
            } elseif ($this->option('update')) {
                $template = 'Update {ResourceName} Table';
            } else {
                $template = now()->format('Y_m_d_His');
            }

            $resourceName = (
                $this->option('model')
                    ? Str::plural($this->option('model'))
                    : null
                )
                ?? (
                    $this->option('table')
                        ? Str::replace('_', ' ', $this->option('table'))
                        : ''
                );
            $name = Str::replace('{ResourceName}', $resourceName, $template);
        }
        return $name;
    }

    /**
     * Prepare variables for stubs.
     */
    protected function prepareVars(): array
    {
        $parts = explode('.', $this->getPluginIdentifier());
        $plugin = array_pop($parts);
        $author = array_pop($parts);
        $name = $this->getNameInput();
        $table = $this->option('table');
        $model = $this->option('model');

        if (empty($table) && !empty($model)) {
            $modelClass = "\\{$author}\\{$plugin}\Models\\{$model}";
            if (class_exists($modelClass)) {
                $table = (new $modelClass)->getTable();
            } else {
                throw new InvalidArgumentException("The model [{$modelClass}] does not exist.");
            }
        }

        if ($this->option('create') && $this->option('update')) {
            throw new InvalidArgumentException('The create & update options cannot both be set at the same time');
        }

        if ($this->option('create')) {
            $scaffold = 'create';
        } elseif ($this->option('update')) {
            $scaffold = 'update';
        } else {
            $scaffold = 'migrate';
        }

        if (in_array($scaffold, ['create', 'update']) && empty($table)) {
            throw new InvalidArgumentException('The table or model options are required when using the create or update options');
        }

        $this->stubs = $this->migrationScaffolds[$scaffold];

        if (!empty($this->option('for-version'))) {
            $version = $this->option('for-version');
        } else {
            $currentVersion = $this->getPlugin()->getPluginVersion();
            if ($currentVersion === VersionManager::NO_VERSION_VALUE) {
                throw new InvalidArgumentException('The plugin [' . $this->getPluginIdentifier() . '] does not have a version set and no --version option was provided. Please set a version in the plugin\'s updates/version.yaml file.');
            }
            $version = $this->getNextVersion($currentVersion);
        }

        $vars = [
            'name' => $name,
            'author' => $author,
            'plugin' => $plugin,
            'version' => $version,
        ];

        if (!empty($model)) {
            $vars['model'] = $model;
        }
        if (!empty($table)) {
            $vars['table'] = $table;
        }

        return $vars;
    }

    /**
     * Create vars for model fields mappings so they can be used in update/create stubs
     */
    protected function processVars(array $vars): array
    {
        $vars = parent::processVars($vars);

        // --model option needed below
        if (empty($vars['model'])) {
            return $vars;
        }

        $vars['fields'] = [];

        $fields_path = $vars['plugin_url'] . '/models/' . $vars['lower_model'] . '/fields.yaml';
        $fields = Yaml::parseFile(plugins_path($fields_path));

        $modelName = $vars['plugin_namespace'] . '\\Models\\' . $vars['model'];

        $vars['model'] = $model = new $modelName();

        foreach (['fields', 'tabs', 'secondaryTabs'] as $type) {
            if (!isset($fields[$type])) {
                continue;
            }
            if ($type === 'fields') {
                $fieldList = $fields[$type];
            } else {
                $fieldList = $fields[$type]['fields'];
            }

            foreach ($fieldList as $field => $config) {
                if (str_contains($field, '@')) {
                    list($field, $context) = explode('@', $field);
                }

                $type = $config['type'] ?? 'text';

                if (str_starts_with($field, '_')
                    || $field === $model->getKeyName()
                    || str_contains($field, '[')
                    || in_array($type, ['fileupload', 'relation', 'relationmanager', 'repeater', 'section', 'hint'])
                    || in_array($field, $model->purgeable ?? [])
                    || $model->getRelationType($field)
                ) {
                    continue;
                }

                $vars['fields'][$field] = $this->mapFieldType($field, $config, $model);
            }
        }

        foreach ($model->getRelationDefinitions() as $relationType => $definitions) {
            if (in_array($relationType, ['belongsTo', 'hasOne'])) {
                foreach (array_keys($definitions) as $relation) {
                    $vars['fields'][$relation . '_id'] = [
                        'type' => 'foreignId',
                        'index' => true,
                        'required' => true,
                    ];
                }
            }
        }

        if ($model->methodExists('getSortOrderColumn')) {
            $field = $model->getSortOrderColumn();
            $vars['fields'][$field] = [
                'type' => 'unsignedinteger',
                'required' => false,
                'index' => true,
            ];
        }

        $vars['primaryKey'] = $model->getKeyName();
        $vars['jsonable'] = $model->getJsonable();
        $vars['timestamps'] = $model->timestamps;

        if ($morphable = $model->morphTo) {
            $vars['morphable'] = array_keys($morphable);
        }

        return $vars;
    }

    /**
     * Get the next version number based on the current number.
     */
    protected function getNextVersion($currentVersion): string
    {
        $currentVersion = ltrim($currentVersion, 'v');
        $parts = explode('.', $currentVersion);
        $parts[count($parts) - 1] = (int) $parts[count($parts) - 1] + 1;
        return 'v' . implode('.', $parts);
    }

    /**
     * Maps model fields config to DB Schema column types.
     */
    protected function mapFieldType(string $fieldName, array $fieldConfig, ?Model $model = null) : array
    {
        switch ($fieldConfig['type'] ?? 'text') {
            case 'checkbox':
            case 'switch':
                $dbType = 'boolean';
                break;
            case 'number':
                if (isset($fieldConfig['step']) && is_int($fieldConfig['step'])) {
                    $dbType = 'integer';
                } else {
                    $dbType = 'double';
                }
                if ($dbType === 'integer' && isset($fieldConfig['min']) && $fieldConfig['min'] >= 0) {
                    $dbType = 'unsignedInteger';
                }
                break;
            case 'range':
                $dbType = 'unsignedInteger';
                break;
            case 'datepicker':
                $dbType = 'datetime';
                if (isset($fieldConfig['mode']) && $fieldConfig['mode']) {
                    $dbType = $fieldConfig['mode'];
                }
                break;
            case 'markdown':
                $dbType = 'mediumText';
                break;
            case 'textarea':
                $dbType = 'text';
                break;
            default:
                $dbType = 'string';
        }

        if ($model) {
            $rule = array_get($model->rules ?? [], $fieldName, '');
            $rule = is_array($rule) ? implode(',', $rule) : $rule;

            $required = str_contains($rule, 'required') ? true : $fieldConfig['required'] ?? false;
        } else {
            $required = $fieldConfig['required'] ?? false;
        }

        return [
            'type' => $dbType,
            'required' => $required,
            'index' => in_array($fieldName, ["slug"]) or str_ends_with($fieldName, "_id"),
        ];
    }
}
