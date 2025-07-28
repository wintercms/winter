// eslint-disable-next-line import/no-extraneous-dependencies
import { createApp } from 'vue';
import MarketPlace from './components/MarketPlace.vue';
import {request, winterRequestPlugin} from './utils/winter-request';

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
            app.request = (handler, options) => Snowboard.request(handler, options);
            app.config.globalProperties.$request = (handler, options) => Snowboard.request(handler, options);
        },
    });

    app.mount(element);
});
