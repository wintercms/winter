import Singleton from '../abstracts/Singleton';

/**
 * Embeds the "extras" stylesheet into the page, if it is not loaded through the theme.
 *
 * @copyright 2021 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class StylesheetLoader extends Singleton {
    /**
     * Defines listeners.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ready: 'ready',
        };
    }

    ready() {
        let stylesLoaded = false;

        // Determine if stylesheet is already loaded
        document.querySelectorAll('link[rel="stylesheet"]').forEach((css) => {
            if (css.href.endsWith('/modules/system/assets/css/snowboard.extras.css')) {
                stylesLoaded = true;
            }
        });

        if (!stylesLoaded) {
            const stylesheet = document.createElement('link');
            stylesheet.setAttribute('rel', 'stylesheet');
            stylesheet.setAttribute('href', this.snowboard.url().asset('/modules/system/assets/css/snowboard.extras.css'));
            document.head.appendChild(stylesheet);
        }
    }
}
