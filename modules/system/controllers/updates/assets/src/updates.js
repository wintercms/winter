// eslint-disable-next-line import/no-extraneous-dependencies
import { createApp } from 'vue';
import PluginMarket from './components/PluginMarket.vue';
import ThemeMarket from './components/PluginMarket.vue';
import { winterRequestPlugin } from './utils/winter-request';

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
        components: { PluginMarket, ThemeMarket },
    });

    app.use(winterRequestPlugin);

    app.mount(element);
});
