import PluginBase from '../abstracts/PluginBase';
import Singleton from '../abstracts/Singleton';
import PluginLoader from './PluginLoader';

import Cookie from '../utilities/Cookie';
import JsonParser from '../utilities/JsonParser';
import Sanitizer from '../utilities/Sanitizer';
import Url from '../utilities/Url';

/**
 * Snowboard - the Winter JavaScript framework.
 *
 * This class represents the base of a modern take on the Winter JS framework, being fully extensible and taking advantage
 * of modern JavaScript features by leveraging the Laravel Mix compilation framework. It also is coded up to remove the
 * dependency of jQuery.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 * @link https://wintercms.com/docs/snowboard/introduction
 */
export default class Snowboard {
    /**
     * Constructor.
     *
     * @param {boolean} autoSingletons Automatically load singletons when DOM is ready. Default: `true`.
     * @param {boolean} debug Whether debugging logs should be shown. Default: `false`.
     */
    constructor(autoSingletons, debug) {
        this.debugEnabled = (typeof debug === 'boolean' && debug === true);
        this.autoInitSingletons = (typeof autoSingletons === 'boolean' && autoSingletons === false);
        this.plugins = {};
        this.foundBaseUrl = null;

        this.attachAbstracts();
        this.loadUtilities();
        this.initialise();

        this.debug('Snowboard framework initialised');
    }

    /**
     * Attaches abstract classes as properties of the Snowboard class.
     *
     * This will allow Javascript functionality with no build process to still extend these abstracts by prefixing
     * them with "Snowboard".
     *
     * ```
     * class MyClass extends Snowboard.PluginBase {
     *     ...
     * }
     * ```
     */
    attachAbstracts() {
        this.PluginBase = PluginBase;
        this.Singleton = Singleton;
    }

    /**
     * Loads the default utilities.
     */
    loadUtilities() {
        this.addPlugin('cookie', Cookie);
        this.addPlugin('jsonParser', JsonParser);
        this.addPlugin('sanitizer', Sanitizer);
        this.addPlugin('url', Url);
    }

    /**
     * Initialises the framework.
     *
     * Attaches a listener for the DOM being ready and triggers a global "ready" event for plugins to begin attaching
     * themselves to the DOM.
     */
    initialise() {
        window.addEventListener('DOMContentLoaded', () => {
            if (this.autoInitSingletons) {
                this.initialiseSingletons();
            }
            this.globalEvent('ready');
        });
    }

    /**
     * Initialises an instance of every singleton.
     */
    initialiseSingletons() {
        Object.values(this.plugins).forEach((plugin) => {
            if (plugin.isSingleton() && plugin.dependenciesFulfilled()) {
                plugin.initialiseSingleton();
            }
        });
    }

    /**
     * Adds a plugin to the framework.
     *
     * Plugins are the cornerstone for additional functionality for Snowboard. A plugin must either be an ES2015 class
     * that extends the PluginBase or Singleton abstract classes, or a simple callback function.
     *
     * When a plugin is added, it is automatically assigned as a new magic method in the Snowboard class using the name
     * parameter, and can be called via this method. This method will always be the "lowercase" version of this name.
     *
     * For example, if a plugin is assigned to the name "myPlugin", it can be called via `Snowboard.myplugin()`.
     *
     * @param {string} name
     * @param {PluginBase|Function} instance
     */
    addPlugin(name, instance) {
        const lowerName = name.toLowerCase();

        if (this.hasPlugin(lowerName)) {
            throw new Error(`A plugin called "${name}" is already registered.`);
        }

        if (typeof instance !== 'function' && instance instanceof PluginBase === false) {
            throw new Error('The provided plugin must extend the PluginBase class, or must be a callback function.');
        }

        if (this[name] !== undefined || this[lowerName] !== undefined) {
            throw new Error('The given name is already in use for a property or method of the Snowboard class.');
        }

        this.plugins[lowerName] = new PluginLoader(lowerName, this, instance);
        const callback = (...parameters) => this.plugins[lowerName].getInstance(...parameters);
        this[name] = callback;
        this[lowerName] = callback;

        this.debug(`Plugin "${name}" registered`);

        // Check if any singletons now have their dependencies fulfilled, and fire their "ready" handler.
        Object.values(this.getPlugins()).forEach((plugin) => {
            if (
                plugin.isSingleton()
                && !plugin.isInitialised()
                && plugin.dependenciesFulfilled()
                && plugin.hasMethod('listens')
                && Object.keys(plugin.callMethod('listens')).includes('ready')
            ) {
                const readyMethod = plugin.callMethod('listens').ready;
                plugin.callMethod(readyMethod);
            }
        });
    }

    /**
     * Removes a plugin.
     *
     * Removes a plugin from Snowboard, calling the destructor method for all active instances of the plugin.
     *
     * @param {string} name
     * @returns {void}
     */
    removePlugin(name) {
        const lowerName = name.toLowerCase();

        if (!this.hasPlugin(lowerName)) {
            this.debug(`Plugin "${name}" already removed`);
            return;
        }

        // Call destructors for all instances
        this.plugins[lowerName].getInstances().forEach((instance) => {
            instance.destructor();
        });

        delete this.plugins[lowerName];
        delete this[lowerName];
        delete this[name];

        this.debug(`Plugin "${name}" removed`);
    }

    /**
     * Determines if a plugin has been registered and is active.
     *
     * A plugin that is still waiting for dependencies to be registered will not be active.
     *
     * @param {string} name
     * @returns {boolean}
     */
    hasPlugin(name) {
        const lowerName = name.toLowerCase();

        return (this.plugins[lowerName] !== undefined);
    }

    /**
     * Returns an array of registered plugins as PluginLoader objects.
     *
     * @returns {PluginLoader[]}
     */
    getPlugins() {
        return this.plugins;
    }

    /**
     * Returns an array of registered plugins, by name.
     *
     * @returns {string[]}
     */
    getPluginNames() {
        return Object.keys(this.plugins);
    }

    /**
     * Returns a PluginLoader object of a given plugin.
     *
     * @returns {PluginLoader}
     */
    getPlugin(name) {
        if (!this.hasPlugin(name)) {
            throw new Error(`No plugin called "${name}" has been registered.`);
        }

        return this.plugins[name];
    }

    /**
     * Finds all plugins that listen to the given event.
     *
     * This works for both normal and promise events. It does NOT check that the plugin's listener actually exists.
     *
     * @param {string} eventName
     * @returns {string[]} The name of the plugins that are listening to this event.
     */
    listensToEvent(eventName) {
        const plugins = [];

        Object.entries(this.plugins).forEach((entry) => {
            const [name, plugin] = entry;

            if (plugin.isFunction()) {
                return;
            }
            if (!plugin.dependenciesFulfilled()) {
                return;
            }
            if (!plugin.hasMethod('listens')) {
                return;
            }

            const listeners = plugin.callMethod('listens');

            if (typeof listeners[eventName] === 'string') {
                plugins.push(name);
            }
        });

        return plugins;
    }

    /**
     * Calls a global event to all registered plugins.
     *
     * If any plugin returns a `false`, the event is considered cancelled.
     *
     * @param {string} eventName
     * @returns {boolean} If event was not cancelled
     */
    globalEvent(eventName, ...parameters) {
        this.debug(`Calling global event "${eventName}"`);

        // Find out which plugins listen to this event - if none listen to it, return true.
        const listeners = this.listensToEvent(eventName);
        if (listeners.length === 0) {
            this.debug(`No listeners found for global event "${eventName}"`);
            return true;
        }
        this.debug(`Listeners found for global event "${eventName}": ${listeners.join(', ')}`);

        let cancelled = false;

        listeners.forEach((name) => {
            const plugin = this.getPlugin(name);

            if (plugin.isFunction()) {
                return;
            }
            if (plugin.isSingleton() && plugin.getInstances().length === 0) {
                plugin.initialiseSingleton();
            }

            const listenMethod = plugin.callMethod('listens')[eventName];

            // Call event handler methods for all plugins, if they have a method specified for the event.
            plugin.getInstances().forEach((instance) => {
                // If a plugin has cancelled the event, no further plugins are considered.
                if (cancelled) {
                    return;
                }

                if (!instance[listenMethod]) {
                    throw new Error(`Missing "${listenMethod}" method in "${name}" plugin`);
                }

                if (instance[listenMethod](...parameters) === false) {
                    cancelled = true;
                    this.debug(`Global event "${eventName}" cancelled by "${name}" plugin`);
                }
            });
        });

        return !cancelled;
    }

    /**
     * Calls a global event to all registered plugins, expecting a Promise to be returned by all.
     *
     * This collates all plugins responses into one large Promise that either expects all to be resolved, or one to reject.
     * If no listeners are found, a resolved Promise is returned.
     *
     * @param {string} eventName
     */
    globalPromiseEvent(eventName, ...parameters) {
        this.debug(`Calling global promise event "${eventName}"`);

        // Find out which plugins listen to this event - if none listen to it, return a resolved promise.
        const listeners = this.listensToEvent(eventName);
        if (listeners.length === 0) {
            this.debug(`No listeners found for global promise event "${eventName}"`);
            return Promise.resolve();
        }
        this.debug(`Listeners found for global promise event "${eventName}": ${listeners.join(', ')}`);

        const promises = [];

        listeners.forEach((name) => {
            const plugin = this.getPlugin(name);

            if (plugin.isFunction()) {
                return;
            }
            if (plugin.isSingleton() && plugin.getInstances().length === 0) {
                plugin.initialiseSingleton();
            }

            const listenMethod = plugin.callMethod('listens')[eventName];

            // Call event handler methods for all plugins, if they have a method specified for the event.
            plugin.getInstances().forEach((instance) => {
                const instancePromise = instance[listenMethod](...parameters);
                if (instancePromise instanceof Promise === false) {
                    return;
                }

                promises.push(instancePromise);
            });
        });

        if (promises.length === 0) {
            return Promise.resolve();
        }

        return Promise.all(promises);
    }

    /**
     * Log a debug message.
     *
     * These messages are only shown when debugging is enabled.
     *
     * @returns {void}
     */
    debug(...parameters) {
        if (!this.debugEnabled) {
            return;
        }

        /* eslint-disable */
        console.groupCollapsed('%c[Snowboard]', 'color: rgb(45, 167, 199); font-weight: normal;', ...parameters);
        console.trace();
        console.groupEnd();
        /* eslint-enable */
    }
}
