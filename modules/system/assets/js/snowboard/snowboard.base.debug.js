import { Snowboard } from '@wintercms/snowboard';

((window) => {
    const instance = new Snowboard(true);

    // Cover all aliases
    window.snowboard = instance;
    window.Snowboard = instance;
    window.SnowBoard = instance;
})(window);
