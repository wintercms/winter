import BaseCookie from 'js-cookie';
import Singleton from '../abstracts/Singleton';

/**
 * Cookie utility.
 *
 * This utility is a thin wrapper around the "js-cookie" library.
 *
 * @see https://github.com/js-cookie/js-cookie
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class Cookie extends Singleton {
    construct() {
        this.defaults = {
            expires: null,
            path: '/',
            domain: null,
            secure: false,
            sameSite: 'Lax',
        };
    }

    /**
     * Set the default cookie parameters for all subsequent "set" and "remove" calls.
     *
     * @param {Object} options
     */
    setDefaults(options) {
        if (typeof options !== 'object') {
            throw new Error('Cookie defaults must be provided as an object');
        }

        Object.entries(options).forEach((entry) => {
            const [key, value] = entry;

            if (this.defaults[key] !== undefined) {
                this.defaults[key] = value;
            }
        });
    }

    /**
     * Get the current default cookie parameters.
     *
     * @returns {Object}
     */
    getDefaults() {
        const defaults = {};

        Object.entries(this.defaults).forEach((entry) => {
            const [key, value] = entry;

            if (this.defaults[key] !== null) {
                defaults[key] = value;
            }
        });

        return defaults;
    }

    /**
     * Get a cookie by name.
     *
     * If `name` is undefined, returns all cookies as an Object.
     *
     * @param {String} name
     * @returns {Object|String}
     */
    get(name) {
        if (name === undefined) {
            const cookies = BaseCookie.get();

            Object.entries(cookies).forEach((entry) => {
                const [cookieName, cookieValue] = entry;

                this.snowboard.globalEvent('cookie.get', cookieName, cookieValue, (newValue) => {
                    cookies[cookieName] = newValue;
                });
            });

            return cookies;
        }

        let value = BaseCookie.get(name);

        // Allow plugins to override the gotten value
        this.snowboard.globalEvent('cookie.get', name, value, (newValue) => {
            value = newValue;
        });

        return value;
    }

    /**
     * Set a cookie by name.
     *
     * You can specify additional cookie parameters through the "options" parameter.
     *
     * @param {String} name
     * @param {String} value
     * @param {Object} options
     * @returns {String}
     */
    set(name, value, options) {
        let saveValue = value;

        // Allow plugins to override the value to save
        this.snowboard.globalEvent('cookie.set', name, value, (newValue) => {
            saveValue = newValue;
        });

        return BaseCookie.set(name, saveValue, {
            ...this.getDefaults(),
            ...options,
        });
    }

    /**
     * Remove a cookie by name.
     *
     * You can specify the additional cookie parameters via the "options" parameter.
     *
     * @param {String} name
     * @param {Object} options
     * @returns {void}
     */
    remove(name, options) {
        BaseCookie.remove(name, {
            ...this.getDefaults(),
            ...options,
        });
    }
}
