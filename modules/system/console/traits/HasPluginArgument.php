<?php namespace System\Console\Traits;

use InvalidArgumentException;
use System\Classes\PluginBase;
use System\Classes\PluginManager;
use Throwable;
use Winter\Storm\Support\Arr;

use function Laravel\Prompts\search;
use function Laravel\Prompts\select;

/**
 * Console Command Trait that provides autocompletion for the "plugin" argument
 *
 * @package winter\wn-system-module
 * @author Luke Towers
 */
trait HasPluginArgument
{
    /**
     * @var string What type of plugins to suggest in the CLI autocompletion. Valid values: "enabled", "disabled", "all"
     */
    // protected $hasPluginsFilter = 'enabled';

    /**
     * @var bool Validate the provided plugin input against the PluginManager, default true.
     */
    // protected $validatePluginInput = true;

    protected ?PluginBase $plugin = null;

    /**
     * Return available plugins for autocompletion of the "plugin" argument
     */
    public function suggestPluginValues()
    {
        $manager = PluginManager::instance();
        $plugins = array_keys($manager->getAllPlugins());
        $filter = $this->hasPluginsFilter ?? 'enabled';

        // Apply the hasPluginsFilter on the list of plugins to return
        if ($filter !== 'all') {
            foreach ($plugins as $i => $identifier) {
                $disabled = $manager->isDisabled($identifier);

                if (
                    (!$disabled && $filter === 'disabled')
                    || ($disabled && $filter === 'enabled')
                ) {
                    unset($plugins[$i]);
                }
            }
        }

        return Arr::sort($plugins);
    }

    /**
     * Prompt for which provider or tag to publish.
     */
    protected function promptForPlugin(): ?string
    {
        $choices = $this->suggestPluginValues();

        $choice = windows_os()
            ? select(
                "Which plugin would you like to select?",
                $choices,
                scroll: 15,
            )
            : search(
                label: "Which plugin would you like to select?",
                placeholder: 'Search...',
                options: fn ($search) => array_values(array_filter(
                    $choices,
                    fn ($choice) => str_contains(strtolower($choice), strtolower($search))
                )),
                scroll: 15,
            );

        return $choice;
    }

    /**
     * Get the desired plugin name from the input.
     * @throws InvalidArgumentException if the provided plugin name is invalid
     */
    public function getPluginIdentifier($identifier = null): string
    {
        $pluginManager = PluginManager::instance();
        $pluginName = $identifier ?? $this->argument('plugin') ?? $this->promptForPlugin();
        $pluginName = $pluginManager->normalizeIdentifier($pluginName);

        if (
            (isset($this->validatePluginInput) && $this->validatePluginInput !== false)
            && !$pluginManager->hasPlugin($pluginName)
        ) {
            throw new InvalidArgumentException(sprintf('Plugin "%s" could not be found.', $pluginName));
        }

        return $pluginName;
    }

    /**
     * Get the plugin instance for the input.
     * @throws InvalidArgumentException if the provided plugin name is invalid
     */
    public function getPlugin($identifier = null): ?PluginBase
    {
        return PluginManager::instance()->findByIdentifier($this->getPluginIdentifier($identifier));
    }

    /**
     * Validates that the provided plugin can be accessed
     *
     * @throws InvalidArgumentException if it cannot be
     */
    public function validateProvidedPlugin()
    {
        if (!$this->plugin) {
            try {
                $this->plugin = $this->getPlugin();
            } catch (Throwable $e) {
                throw new InvalidArgumentException(sprintf(
                    'Provided plugin "%s" failed to load: %s',
                    $this->argument('plugin'),
                    $e->getMessage()
                ));
            }
        }
    }
}
