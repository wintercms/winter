import Snowboard from './main/Snowboard';

((window) => {
    const snowboard = new Snowboard(true, true);

    // Cover all aliases
    window.snowboard = snowboard;
    window.Snowboard = snowboard;
    window.SnowBoard = snowboard;
})(window);
