import Snowboard from './main/Snowboard';
import ProxyHandler from './main/ProxyHandler';

((window) => {
    const snowboard = new Proxy(
        new Snowboard(true, true),
        ProxyHandler,
    );

    // Cover all aliases
    window.snowboard = snowboard;
    window.Snowboard = snowboard;
    window.SnowBoard = snowboard;
})(window);
