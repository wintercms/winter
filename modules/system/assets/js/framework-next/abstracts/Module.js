import Winter from '../main/Winter';

/**
 * Module abstract.
 *
 * This class provides the base functionality for all modules.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class Module {
    /**
     * Constructor.
     *
     * The constructor is provided the Winter framework instance.
     *
     * @param {Winter} winter
     */
    constructor(winter) {
        this.winter = winter;
    }

    /**
     * Defines the required modules for this specific module to work.
     *
     * @returns {string[]} An array of modules required for this module to work, as strings.
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
     * Fired when this module is removed.
     */
    destructor() {
        this.detach();
        delete this.winter;
    }
}
