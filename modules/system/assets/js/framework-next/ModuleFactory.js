/**
 * Module factory class.
 *
 * This is a provider class for a single module and provides the link between Winter framework functionality and the
 * underlying module instances.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
class ModuleFactory {
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
        if (this.singleton) {
            if (this.instances.length === 0) {
                const newInstance = new this.instance(this.winter, ...arguments);
                newInstance.detach = () => this.instances.splice(this.instances.indexOf(newInstance), 1);
                this.instances.push(newInstance);
            }

            return this.instances[0];
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
}
