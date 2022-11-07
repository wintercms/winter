/**
 * Plugin base abstract.
 *
 * This class provides the base functionality for all plugins.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class PluginBase {
    /**
     * Constructor.
     *
     * The constructor is provided the Snowboard framework instance, and should not be overwritten
     * unless you absolutely know what you're doing.
     *
     * @param {Snowboard} snowboard
     */
    constructor(snowboard) {
        this.snowboard = snowboard;
    }

    /**
     * Plugin constructor.
     *
     * This method should be treated as the true constructor of a plugin, and can be overwritten.
     * It will be called straight after construction.
     */
    construct() {
    }

    /**
     * Defines the required plugins for this specific module to work.
     *
     * @returns {string[]} An array of plugins required for this module to work, as strings.
     */
    dependencies() {
        return [];
    }

    /**
     * Defines the listener methods for global events.
     *
     * @returns {Object}
     */
    listens() {
        return {};
    }

    /**
     * Plugin destructor.
     *
     * Fired when this plugin is removed. Can be manually called if you have another scenario for
     * destruction, ie. the element attached to the plugin is removed or changed.
     */
    destruct() {
        this.detach();
        delete this.snowboard;
    }

    /**
     * Plugin destructor (old method name).
     *
     * Allows previous usage of the "destructor" method to still work.
     */
    destructor() {
        this.destruct();
    }
}
