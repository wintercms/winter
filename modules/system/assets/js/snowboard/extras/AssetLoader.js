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
export default class AssetLoader extends window.Snowboard.Singleton {
    /**
     * Event listeners.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ajaxLoadAssets: 'processAssets',
        };
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
     * @param {Object} assets
     * @returns {Promise}
     */
    processAssets(assets) {
        return new Promise((resolve, reject) => {
            const promises = [];

            if (assets.js && assets.js.length > 0) {
                assets.js.forEach((script) => {
                    promises.push(this.loadScript(script));
                });
            }

            if (assets.css && assets.css.length > 0) {
                assets.css.forEach((style) => {
                    promises.push(this.loadStyle(style));
                });
            }

            if (assets.img && assets.img.length > 0) {
                assets.img.forEach((image) => {
                    promises.push(this.loadImage(image));
                });
            }

            if (promises.length === 0) {
                resolve();
            }

            Promise.all(promises).then(
                () => {
                    resolve();
                },
                (error) => {
                    reject(error);
                },
            );
        });
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
            // Check that script is not already loaded
            const loaded = document.querySelector(`script[src="${script}"]`);
            if (loaded) {
                resolve();
            }

            // Create script
            const domScript = document.createElement('script');
            domScript.setAttribute('type', 'text/javascript');
            domScript.setAttribute('src', script);
            domScript.addEventListener('load', () => {
                this.snowboard.globalEvent('backend.assetLoaded', 'script', script, domScript);
                resolve();
            });
            domScript.addEventListener('error', () => {
                this.snowboard.globalEvent('backend.assetError', 'script', script, domScript);
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
     * @param {String} script
     * @returns {Promise}
     */
    loadStyle(style) {
        return new Promise((resolve, reject) => {
            // Check that stylesheet is not already loaded
            const loaded = document.querySelector(`link[rel="stylesheet"][href="${style}"]`);
            if (loaded) {
                resolve();
            }

            // Create stylesheet
            const domCss = document.createElement('link');
            domCss.setAttribute('rel', 'stylesheet');
            domCss.setAttribute('href', style);
            domCss.addEventListener('load', () => {
                this.snowboard.globalEvent('backend.assetLoaded', 'style', style, domCss);
                resolve();
            });
            domCss.addEventListener('error', () => {
                this.snowboard.globalEvent('backend.assetError', 'style', style, domCss);
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
            const img = new Image();
            img.addEventListener('load', () => {
                this.snowboard.globalEvent('backend.assetLoaded', 'image', image, img);
                resolve();
            });
            img.addEventListener('error', () => {
                this.snowboard.globalEvent('backend.assetError', 'image', image, img);
                reject(new Error(`Unable to load image file: "${image}"`));
            });
            img.src = image;
        });
    }
}