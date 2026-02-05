// eslint-disable-next-line import/no-extraneous-dependencies
import { createApp } from 'vue';
import VueTippy from 'vue-tippy';
import 'tippy.js/dist/tippy.css';
import MarketPlace from './components/MarketPlace.vue';

const onReady = (callback) => {
    if (document.readyState === 'complete') {
        callback();
    } else {
        window.addEventListener('load', callback);
    }
};

onReady(() => {
    const element = document.querySelector('#updates-app');

    const app = createApp({
        ...element.dataset,
        components: { MarketPlace },
    });

    app.use({
        install(app) {
            // Add request to vuw
            app.request = (handler, options) => Snowboard.request(handler, options);
            app.config.globalProperties.$request = (handler, options) => Snowboard.request(handler, options);

            // Add popup to vue
            app.popup = (options) => typeof $ !== 'undefined' ? $.popup(options) : null;
            app.config.globalProperties.$popup = (options) => typeof $ !== 'undefined' ? $.popup(options) : null;
        },
    });

    app.use(VueTippy, {
        defaultProps: {
            placement: 'top',
        }
    });

    app.mount(element);
});
