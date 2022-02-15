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
     * The constructor is provided the Snowboard framework instance.
     *
     * @param {Snowboard} snowboard
     */
    constructor(snowboard) {
        this.snowboard = snowboard;
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
     * Destructor.
     *
     * Fired when this plugin is removed.
     */
    destructor() {
        this.detach();
        delete this.snowboard;
    }
}
