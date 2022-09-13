import PluginBase from '../abstracts/PluginBase';
import Singleton from '../abstracts/Singleton';
import InnerProxyHandler from './InnerProxyHandler';

/**
 * Plugin loader class.
 *
 * This is a provider (factory) class for a single plugin and provides the link between Snowboard framework functionality
 * and the underlying plugin instances. It also provides some basic mocking of plugin methods for testing.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class PluginLoader {
    /**
     * Constructor.
     *
     * Binds the Winter framework to the instance.
     *
     * @param {string} name
     * @param {Snowboard} snowboard
     * @param {PluginBase} instance
     */
    constructor(name, snowboard, instance) {
        this.name = name;
        this.snowboard = new Proxy(
            snowboard,
            InnerProxyHandler,
        );
        this.instance = instance;

        // Freeze instance that has been inserted into this loader
        Object.freeze(this.instance);

        this.instances = [];
        this.singleton = {
            initialised: false,
        };
        // Prevent further extension of the singleton status object
        Object.seal(this.singleton);

        this.mocks = {};
        this.originalFunctions = {};

        // Freeze loader itself
        Object.freeze(PluginLoader.prototype);
        Object.freeze(this);
    }

    /**
     * Determines if the current plugin has a specific method available.
     *
     * Returns false if the current plugin is a callback function.
     *
     * @param {string} methodName
     * @returns {boolean}
     */
    hasMethod(methodName) {
        if (this.isFunction()) {
            return false;
        }

        return (typeof this.instance.prototype[methodName] === 'function');
    }

    /**
     * Calls a prototype method for a plugin. This should generally be used for "static" calls.
     *
     * @param {string} methodName
     * @param {...} args
     * @returns {any}
     */
    callMethod(...parameters) {
        if (this.isFunction()) {
            return null;
        }

        const args = parameters;
        const methodName = args.shift();

        return this.instance.prototype[methodName](args);
    }

    /**
     * Returns an instance of the current plugin.
     *
     * - If this is a callback function plugin, the function will be returned.
     * - If this is a singleton, the single instance of the plugin will be returned.
     *
     * @returns {PluginBase|Function}
     */
    getInstance(...parameters) {
        if (this.isFunction()) {
            return this.instance(...parameters);
        }
        if (!this.dependenciesFulfilled()) {
            const unmet = this.getDependencies().filter((item) => !this.snowboard.getPluginNames().includes(item));
            throw new Error(`The "${this.name}" plugin requires the following plugins: ${unmet.join(', ')}`);
        }
        if (this.isSingleton()) {
            if (this.instances.length === 0) {
                this.initialiseSingleton(...parameters);
            }

            // Apply mocked methods
            if (Object.keys(this.mocks).length > 0) {
                Object.entries(this.originalFunctions).forEach((entry) => {
                    const [methodName, callback] = entry;
                    this.instances[0][methodName] = callback;
                });
                Object.entries(this.mocks).forEach((entry) => {
                    const [methodName, callback] = entry;
                    this.instances[0][methodName] = (...params) => callback(this, ...params);
                });
            }

            return this.instances[0];
        }

        // Apply mocked methods to prototype
        if (Object.keys(this.mocks).length > 0) {
            Object.entries(this.originalFunctions).forEach((entry) => {
                const [methodName, callback] = entry;
                this.instance.prototype[methodName] = callback;
            });
            Object.entries(this.mocks).forEach((entry) => {
                const [methodName, callback] = entry;
                this.instance.prototype[methodName] = (...params) => callback(this, ...params);
            });
        }

        const newInstance = new this.instance(this.snowboard, ...parameters);
        newInstance.detach = () => this.instances.splice(this.instances.indexOf(newInstance), 1);
        newInstance.construct(...parameters);
        this.instances.push(newInstance);

        return newInstance;
    }

    /**
     * Gets all instances of the current plugin.
     *
     * If this plugin is a callback function plugin, an empty array will be returned.
     *
     * @returns {PluginBase[]}
     */
    getInstances() {
        if (this.isFunction()) {
            return [];
        }

        return this.instances;
    }

    /**
     * Determines if the current plugin is a simple callback function.
     *
     * @returns {boolean}
     */
    isFunction() {
        return (typeof this.instance === 'function' && this.instance.prototype instanceof PluginBase === false);
    }

    /**
     * Determines if the current plugin is a singleton.
     *
     * @returns {boolean}
     */
    isSingleton() {
        return this.instance.prototype instanceof Singleton === true;
    }

    /**
     * Determines if a singleton has been initialised.
     *
     * Normal plugins will always return true.
     *
     * @returns {boolean}
     */
    isInitialised() {
        if (!this.isSingleton()) {
            return true;
        }

        return this.singleton.initialised;
    }

    /**
     * Initialises the singleton instance.
     *
     * @returns {void}
     */
    initialiseSingleton(...parameters) {
        if (!this.isSingleton()) {
            return;
        }

        const newInstance = new this.instance(this.snowboard, ...parameters);
        newInstance.detach = () => this.instances.splice(this.instances.indexOf(newInstance), 1);
        newInstance.construct(...parameters);
        this.instances.push(newInstance);
        this.singleton.initialised = true;
    }

    /**
     * Gets the dependencies of the current plugin.
     *
     * @returns {string[]}
     */
    getDependencies() {
        // Callback functions cannot have dependencies.
        if (this.isFunction()) {
            return [];
        }

        // No dependency method specified.
        if (typeof this.instance.prototype.dependencies !== 'function') {
            return [];
        }

        return this.instance.prototype.dependencies().map((item) => item.toLowerCase());
    }

    /**
     * Determines if the current plugin has all its dependencies fulfilled.
     *
     * @returns {boolean}
     */
    dependenciesFulfilled() {
        const dependencies = this.getDependencies();

        let fulfilled = true;
        dependencies.forEach((plugin) => {
            if (!this.snowboard.hasPlugin(plugin)) {
                fulfilled = false;
            }
        });

        return fulfilled;
    }

    /**
     * Allows a method of an instance to be mocked for testing.
     *
     * This mock will be applied for the life of an instance. For singletons, the mock will be applied for the life
     * of the page.
     *
     * Mocks cannot be applied to callback function plugins.
     *
     * @param {string} methodName
     * @param {Function} callback
     */
    mock(methodName, callback) {
        if (this.isFunction()) {
            return;
        }

        if (!this.instance.prototype[methodName]) {
            throw new Error(`Function "${methodName}" does not exist and cannot be mocked`);
        }

        this.mocks[methodName] = callback;
        this.originalFunctions[methodName] = this.instance.prototype[methodName];

        if (this.isSingleton() && this.instances.length === 0) {
            this.initialiseSingleton();

            // Apply mocked method
            this.instances[0][methodName] = (...parameters) => callback(this, ...parameters);
        }
    }

    /**
     * Removes a mock callback from future instances.
     *
     * @param {string} methodName
     */
    unmock(methodName) {
        if (this.isFunction()) {
            return;
        }
        if (!this.mocks[methodName]) {
            return;
        }

        if (this.isSingleton()) {
            this.instances[0][methodName] = this.originalFunctions[methodName];
        }

        delete this.mocks[methodName];
        delete this.originalFunctions[methodName];
    }
}
