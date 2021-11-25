import ModuleFactory from './ModuleFactory';
import ModuleAbstract from './abstracts/Module.js';

import Debounce from './utilities/Debounce';
import JsonParser from './utilities/JsonParser';
import Sanitizer from './utilities/Sanitizer';

/**
 * Winter JavaScript framework.
 *
 * This class represents the base of a modern take on the Winter JS framework, being fully modular and taking advantage
 * of modern JavaScript features by leveraging the Laravel Mix compilation framework. It also is coded up to remove the
 * dependency of jQuery.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 * @link https://wintercms.com/docs/ajax/introduction
 */
export default class Winter {
    /**
     * Constructor.
     *
     * @param {boolean} debug Whether debugging logs should be shown.
     */
    constructor(debug) {
        /* develblock:start */
        this.debugEnabled = (typeof debug === 'boolean' && debug === true) ? true : false;
        /* develblock:end */
        this.modules = {};

        this.initialise();

        /* develblock:start */
        this.debug('Winter framework initialised');
        /* develblock:end */
    }

    /**
     * Initialises the framework.
     *
     * Attaches a listener for the DOM being ready and triggers a global "ready" event for modules to begin attaching
     * themselves to the DOM.
     */
    initialise() {
        this.loadDefaultUtilities();

        window.addEventListener('DOMContentLoaded', () => {
            this.initialiseSingletons();
            this.globalEvent('ready');
        });
    }

    /**
     * Initialises an instance of every singleton.
     */
    initialiseSingletons() {
        Object.values(this.modules).forEach((mod) => {
            if (mod.isSingleton()) {
                mod.getInstance();
            }
        });
    }

    /**
     * Loads default global utilities used in the Winter framework.
     */
    loadDefaultUtilities() {
        this.addModule('debounce', Debounce);
        this.addModule('jsonParser', JsonParser);
        this.addModule('sanitizer', Sanitizer);
    }

    /**
     * Add a module to the framework.
     *
     * Modules are the cornerstone for additional functionality for the Winter JavaScript framework. A module must either
     * be a ES2015 class that extends the Module abstract class, or a simple callback function.
     *
     * For module classes, you can opt to extend the Singleton abstract class to define the module as a singleton, in
     * which the same instance will be used for all uses of the module.
     *
     * @param {string} name
     * @param {ModuleAbstract|Function} instance
     */
    addModule(name, instance) {
        const lowerName = name.toLowerCase();

        if (this.hasModule(lowerName)) {
            throw new Error(`A module called "${name}" is already registered.`);
        }

        if (typeof instance !== 'function' && instance instanceof ModuleAbstract === false) {
            throw new Error(`The provided module must be a Module class instance or callback function.`);
        }

        this.modules[lowerName] = new ModuleFactory(lowerName, this, instance);
        this[lowerName] = function () {
            return this.modules[lowerName].getInstance(...arguments);
        };
        /* develblock:start */
        this.debug(`Module "${name}" registered`);
        /* develblock:end */
    }

    /**
     * Removes a module.
     *
     * Removes a module from the Winter JavaScript framework, calling the destructor method for all active instances of
     * the module.
     *
     * @param {string} name
     * @returns {void}
     */
    removeModule(name) {
        const lowerName = name.toLowerCase();

        if (this.modules[lowerName] === undefined) {
            /* develblock:start */
            this.debug(`Module "${name}" already removed`);
            /* develblock:end */
            return;
        }

        // Call destructors for all instances
        this.modules[lowerName].getInstances().forEach((instance) => {
            instance.destructor();
        });

        delete this.modules[lowerName];
        delete this[lowerName];
        /* develblock:start */
        this.debug(`Module "${name}" removed`);
        /* develblock:end */
    }

    /**
     * Determines if a module has been registered and is active.
     *
     * A module that is still waiting for dependencies to be registered will not be active.
     *
     * @param {string} name
     * @returns {boolean}
     */
    hasModule(name) {
        const lowerName = name.toLowerCase();

        return (this.modules[lowerName] !== undefined);
    }

    /**
     * Returns an array of registered modules as ModuleFactory objects.
     *
     * @returns {ModuleFactory[]}
     */
    getModules() {
        return this.modules;
    }

    /**
     * Returns an array of registered modules, by name.
     *
     * @returns {string[]}
     */
    getModuleNames() {
        return Object.keys(this.modules);
    }

    /**
     * Finds all modules that listen to the given event.
     *
     * This works for both normal and promise events.
     *
     * @param {string} eventName
     * @returns {string[]} The name of the modules that are listening to this event.
     */
    listensToEvent(eventName) {
        const modules = [];

        for (const [name, mod] of Object.entries(this.modules)) {
            if (mod.isFunction()) {
                continue;
            }

            if (mod.hasMethod(eventName)) {
                modules.push(name);
            }
        }

        return modules;
    }

    /**
     * Calls a global event to all registered modules.
     *
     * If any module returns a `false`, the event is considered cancelled.
     *
     * @param {string} eventName
     * @returns {boolean} If event was not cancelled
     */
    globalEvent(eventName) {
        /* develblock:start */
        this.debug(`Calling global event "${eventName}"`);
        /* develblock:end */

        let cancelled = false;
        let args = Array.from(arguments);
        args.shift();

        Object.values(this.modules).forEach((mod) => {
        if (mod.isFunction()) {
            return;
        }

        // Call event handler methods for all modules, if they have a method specified for the event.
        if (mod.hasMethod(eventName)) {
            mod.getInstances().forEach((instance) => {
                // If a module has cancelled the event, no further modules are considered.
                if (cancelled) {
                    return;
                }

                if (instance[eventName](...args) === false) {
                    cancelled = true;
                }
            });
        }
        });

        return !cancelled;
    }

    /**
     * Calls a global event to all registered modules, expecting a Promise to be returned by all.
     *
     * This collates all module responses into one large Promise that either expects all to be resolved, or one to reject.
     * If no listeners are found, a resolved Promise is returned.
     *
     * @param {string} eventName
     */
    globalPromiseEvent(eventName) {
        /* develblock:start */
        this.debug(`Calling global promise event "${eventName}"`);
        /* develblock:end */

        const promises = [];
        let args = Array.from(arguments);
        args.shift();

        Object.values(this.modules).forEach((mod) => {
        if (mod.isFunction()) {
            return;
        }

        // Call event handler methods for all modules, if they have a method specified for the event.
        if (mod.hasMethod(eventName)) {
            mod.getInstances().forEach((instance) => {
                const instancePromise = instance[eventName](...args);
                if (instancePromise instanceof Promise === false) {
                    return;
                }

                promises.push(instancePromise);
            });
        }
        });

        if (promises.length === 0) {
            return Promise.resolve();
        }

        return Promise.all(promises);
    }

    /* develblock:start */
    /**
     * Log a debug message.
     *
     * These messages are only shown when debugging is enabled.
     *
     * @returns {void}
     */
    debug() {
        if (!this.debugEnabled) {
            return;
        }

        console.log("%c[Winter]", "color: rgb(45, 167, 199);", ...arguments);
    }
    /* develblock:end */
}
