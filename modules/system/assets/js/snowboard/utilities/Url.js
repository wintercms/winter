import Singleton from '../abstracts/Singleton';

/**
 * URL utility.
 *
 * This utility provides URL functions.
 *
 * @copyright 2022 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class Url extends Singleton {
    construct() {
        this.foundBaseUrl = null;
        this.foundAssetUrl = null;
        this.baseUrl();
        this.assetUrl();
    }

    /**
     * Gets a URL based on a relative path.
     *
     * If an absolute URL is provided, it will be returned unchanged.
     *
     * @param {string} url
     * @returns {string}
     */
    to(url) {
        const urlRegex = /^(?:[^:]+:\/\/)[-a-z0-9@:%._+~#=]{1,256}\b([-a-z0-9()@:%_+.~#?&//=]*)/i;

        if (url.match(urlRegex)) {
            return url;
        }

        const theUrl = url.replace(/^\/+/, '');

        return `${this.baseUrl()}${theUrl}`;
    }

    /**
     * Gets an Asset URL based on a relative path.
     *
     * If an absolute URL is provided, it will be returned unchanged.
     *
     * @param {string} url
     * @returns {string}
     */
    asset(url) {
        const urlRegex = /^(?:[^:]+:\/\/)[-a-z0-9@:%._+~#=]{1,256}\b([-a-z0-9()@:%_+.~#?&//=]*)/i;

        if (url.match(urlRegex)) {
            return url;
        }

        const theUrl = url.replace(/^\/+/, '');

        return `${this.assetUrl()}${theUrl}`;
    }

    /**
     * Helper method to get the base URL of this install.
     *
     * This determines the base URL from three sources, in order:
     *  - If Snowboard is loaded via the `{% snowboard %}` tag, it will retrieve the base URL that
     * is automatically included there.
     *  - If a `<base>` tag is available, it will use the URL specified in the base tag.
     *  - Finally, it will take a guess from the current location. This will likely not work for sites
     * that reside in subdirectories.
     *
     * The base URL will always contain a trailing backslash.
     *
     * @returns {string}
     */
    baseUrl() {
        if (this.foundBaseUrl !== null) {
            return this.foundBaseUrl;
        }

        if (document.querySelector('script[data-module="snowboard-base"]') !== null) {
            this.foundBaseUrl = this.validateBaseUrl(document.querySelector('script[data-module="snowboard-base"]').dataset.baseUrl);
            return this.foundBaseUrl;
        }

        if (document.querySelector('base') !== null) {
            this.foundBaseUrl = this.validateBaseUrl(document.querySelector('base').getAttribute('href'));
            return this.foundBaseUrl;
        }

        const urlParts = [
            window.location.protocol,
            '//',
            window.location.host,
            '/',
        ];
        this.foundBaseUrl = urlParts.join('');

        return this.foundBaseUrl;
    }

    /**
     * Helper method to get the asset URL of this install.
     *
     * This determines the base URL from three sources, in order:
     *  - If Snowboard is loaded via the `{% snowboard %}` tag, it will retrieve the asset URL that
     * is automatically included there.
     *  - If a `<link rel="asset_url" href="https://example.com">` tag is available, it will use the URL specified in the link tag.
     *  - Finally, it will take a guess from the current location. This will likely not work for sites
     * that reside in subdirectories.
     *
     * The asset URL will always contain a trailing backslash.
     *
     * @returns {string}
     */
    assetUrl() {
        if (this.foundAssetUrl !== null) {
            return this.foundAssetUrl;
        }

        if (document.querySelector('script[data-module="snowboard-base"]') !== null) {
            this.foundAssetUrl = this.validateBaseUrl(document.querySelector('script[data-module="snowboard-base"]').dataset.assetUrl);
            return this.foundAssetUrl;
        }

        if (document.querySelector('link[rel="asset_url"]') !== null) {
            this.foundAssetUrl = this.validateBaseUrl(document.querySelector('link[rel="asset_url"]').getAttribute('href'));
            return this.foundAssetUrl;
        }

        const urlParts = [
            window.location.protocol,
            '//',
            window.location.host,
            '/',
        ];
        this.foundAssetUrl = urlParts.join('');

        return this.foundAssetUrl;
    }

    /**
     * Validates the base URL, ensuring it is a HTTP/HTTPs URL.
     *
     * If the Snowboard script or <base> tag on the page use a different type of URL, this will fail with
     * an error.
     *
     * @param {string} url
     * @returns {string}
     */
    validateBaseUrl(url) {
        const urlRegex = /^(([^:/?#]+):)?(\/\/([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?/i;
        const urlParts = urlRegex.exec(url);
        const protocol = urlParts[2];
        const domain = urlParts[4];

        if (protocol && ['http', 'https'].indexOf(protocol.toLowerCase()) === -1) {
            throw new Error('Invalid base URL detected');
        }
        if (!domain) {
            throw new Error('Invalid base URL detected');
        }

        return (url.substr(-1) === '/')
            ? url
            : `${url}/`;
    }
}
