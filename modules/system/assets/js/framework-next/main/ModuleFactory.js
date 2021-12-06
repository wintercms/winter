import Winter from './Winter';
import Module from '../abstracts/Module';
import Singleton from "../abstracts/Singleton";

/**
 * Module factory class.
 *
 * This is a provider class for a single module and provides the link between Winter framework functionality and the
 * underlying module instances.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class ModuleFactory {
    /**
     * Constructor.
     *
     * Binds the Winter framework to the instance.
     *
     * @param {string} name
     * @param {Winter} winter
     * @param {Module} instance
     */
    constructor(name, winter, instance) {
        this.name = name;
        this.winter = winter;
        this.instance = instance;
        this.instances = [];
        this.singleton = instance.prototype instanceof Singleton;
        this.mocks = {};
        this.originalFunctions = {};
    }

    /**
     * Determines if the current module has a specific method available.
     *
     * Returns false if the current module is a callback function.
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
     * Calls a prototype method for a module. This should generally be used for "static" calls.
     *
     * @param {string} methodName
     * @param {...} args
     * @returns {any}
     */
    callMethod() {
        if (this.isFunction()) {
            return null;
        }

        const args = Array.from(arguments);
        const methodName = args.shift();

        return this.instance.prototype[methodName](args);
    }

    /**
     * Returns an instance of the current module.
     *
     * If this is a callback function module, the function will be returned.
     *
     * If this is a singleton, the single instance of the module will be returned.
     *
     * @returns {Module|Function}
     */
    getInstance() {
        if (this.isFunction()) {
            return this.instance(...arguments);
        }
        if (!this.dependenciesFulfilled()) {
            const unmet = this.getDependencies().filter((item) => !this.winter.getModuleNames().includes(item));
            throw new Error(`The "${this.name}" module requires the following modules: ${unmet.join(', ')}`);
        }
        if (this.isSingleton()) {
            if (this.instances.length === 0) {
                const newInstance = new this.instance(this.winter, ...arguments);
                newInstance.detach = () => this.instances.splice(this.instances.indexOf(newInstance), 1);
                this.instances.push(newInstance);
            }

            // Apply mocked methods
            if (Object.keys(this.mocks).length > 0) {
                for (const [methodName, callback] of Object.entries(this.originalFunctions)) {
                    this.instances[0][methodName] = callback;
                }
                for (const [methodName, callback] of Object.entries(this.mocks)) {
                    this.instances[0][methodName] = function () {
                        return callback(this, ...arguments);
                    };
                }
            }

            return this.instances[0];
        }

        // Apply mocked methods to prototype
        if (Object.keys(this.mocks).length > 0) {
            for (const [methodName, callback] of Object.entries(this.originalFunctions)) {
                this.instance.prototype[methodName] = callback;
            }
            for (const [methodName, callback] of Object.entries(this.mocks)) {
                this.instance.prototype[methodName] = function () {
                    return callback(this, ...arguments);
                };
            }
        }

        const newInstance = new this.instance(this.winter, ...arguments);
        newInstance.detach = () => this.instances.splice(this.instances.indexOf(newInstance), 1);

        this.instances.push(newInstance);
        return newInstance;
    }

    /**
     * Gets all instances of the current module.
     *
     * If this module is a callback function module, an empty array will be returned.
     *
     * @returns {Module[]}
     */
    getInstances() {
        if (this.isFunction()) {
            return [];
        }

        return this.instances;
    }

    /**
     * Determines if the current module is a simple callback function.
     *
     * @returns {boolean}
     */
    isFunction() {
        return (typeof this.instance === 'function' && this.instance.prototype instanceof Module === false);
    }

    /**
     * Determines if the current module is a singleton.
     *
     * @returns {boolean}
     */
    isSingleton() {
        return this.instance.prototype instanceof Singleton === true;
    }

    /**
     * Gets the dependencies of the current module.
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
     * Determines if the current module has all its dependencies fulfilled.
     *
     * @returns {boolean}
     */
    dependenciesFulfilled() {
        const dependencies = this.getDependencies();

        let fulfilled = true;
        dependencies.forEach((module) => {
            if (!this.winter.hasModule(module)) {
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
     * Mocks cannot be applied to function modules.
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
            const newInstance = new this.instance(this.winter, ...arguments);
            newInstance.detach = () => this.instances.splice(this.instances.indexOf(newInstance), 1);
            this.instances.push(newInstance);

            // Apply mocked method
            this.instances[0][methodName] = function () {
                return callback(this, ...arguments);
            };
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
