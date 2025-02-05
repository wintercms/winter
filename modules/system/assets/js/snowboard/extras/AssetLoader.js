import Singleton from '../abstracts/Singleton';

/**
 * Asset Loader.
 *
 * Provides simple asset loading functionality for Snowboard, making it easy to pre-load images or
 * include JavaScript or CSS assets on the fly.
 *
 * By default, this loader will listen to any assets that have been requested to load in an AJAX
 * response, such as responses from a component.
 *
 * You can also load assets manually by calling the following:
 *
 * ```js
 * Snowboard.addPlugin('assetLoader', AssetLoader);
 * Snowboard.assetLoader().processAssets(assets);
 * ```
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class AssetLoader extends Singleton {
    /**
     * Event listeners.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ajaxLoadAssets: 'load',
        };
    }

    /**
     * Dependencies.
     *
     * @returns {Array}
     */
    dependencies() {
        return [
            'url',
        ];
    }

    /**
     * Process and load assets.
     *
     * The `assets` property of this method requires an object with any of the following keys and an
     * array of paths:
     *
     * - `js`: An array of JavaScript URLs to load
     * - `css`: An array of CSS stylesheet URLs to load
     * - `img`: An array of image URLs to pre-load
     *
     * Both `js` and `css` files will be automatically injected, however `img` files will not.
     *
     * This method will return a Promise that resolves when all required assets are loaded. If an
     * asset fails to load, this Promise will be rejected.
     *
     * ESLint *REALLY* doesn't like this code, but ignore it. It's the only way it works.
     *
     * @param {Object} assets
     * @returns {Promise}
     */
    async load(assets) {
        if (assets.js && assets.js.length > 0) {
            for (const script of assets.js) {
                try {
                    await this.loadScript(script);
                } catch (error) {
                    return Promise.reject(error);
                }
            }
        }

        if (assets.css && assets.css.length > 0) {
            for (const style of assets.css) {
                try {
                    await this.loadStyle(style);
                } catch (error) {
                    return Promise.reject(error);
                }
            }
        }

        if (assets.img && assets.img.length > 0) {
            for (const image of assets.img) {
                try {
                    await this.loadImage(image);
                } catch (error) {
                    return Promise.reject(error);
                }
            }
        }

        return Promise.resolve();
    }

    /**
     * Injects and loads a JavaScript URL into the DOM.
     *
     * The script will be appended before the closing `</body>` tag.
     *
     * @param {String} script
     * @returns {Promise}
     */
    loadScript(script) {
        return new Promise((resolve, reject) => {
            // Resolve script URL
            script = this.snowboard.url().asset(script);

            // Check that script is not already loaded
            const loaded = document.querySelector(`script[src="${script}"]`);
            if (loaded) {
                resolve();
                return;
            }

            // Create script
            const domScript = document.createElement('script');
            domScript.setAttribute('type', 'text/javascript');
            domScript.setAttribute('src', script);
            domScript.addEventListener('load', () => {
                this.snowboard.globalEvent('assetLoader.loaded', 'script', script, domScript);
                resolve();
            });
            domScript.addEventListener('error', () => {
                this.snowboard.globalEvent('assetLoader.error', 'script', script, domScript);
                reject(new Error(`Unable to load script file: "${script}"`));
            });
            document.body.append(domScript);
        });
    }

    /**
     * Injects and loads a CSS stylesheet into the DOM.
     *
     * The stylesheet will be appended before the closing `</head>` tag.
     *
     * @param {String} style
     * @returns {Promise}
     */
    loadStyle(style) {
        return new Promise((resolve, reject) => {
            // Resolve style URL
            style = this.snowboard.url().asset(style);

            // Check that stylesheet is not already loaded
            const loaded = document.querySelector(`link[rel="stylesheet"][href="${style}"]`);
            if (loaded) {
                resolve();
                return;
            }

            // Create stylesheet
            const domCss = document.createElement('link');
            domCss.setAttribute('rel', 'stylesheet');
            domCss.setAttribute('href', style);
            domCss.addEventListener('load', () => {
                this.snowboard.globalEvent('assetLoader.loaded', 'style', style, domCss);
                resolve();
            });
            domCss.addEventListener('error', () => {
                this.snowboard.globalEvent('assetLoader.error', 'style', style, domCss);
                reject(new Error(`Unable to load stylesheet file: "${style}"`));
            });
            document.head.append(domCss);
        });
    }

    /**
     * Pre-loads an image.
     *
     * The image will not be injected into the DOM.
     *
     * @param {String} image
     * @returns {Promise}
     */
    loadImage(image) {
        return new Promise((resolve, reject) => {
            // Resolve script URL
            image = this.snowboard.url().asset(image);

            const img = new Image();
            img.addEventListener('load', () => {
                this.snowboard.globalEvent('assetLoader.loaded', 'image', image, img);
                resolve();
            });
            img.addEventListener('error', () => {
                this.snowboard.globalEvent('assetLoader.error', 'image', image, img);
                reject(new Error(`Unable to load image file: "${image}"`));
            });
            img.src = image;
        });
    }
}
