import Snowcart from './main/Snowcart';

((window) => {
    const snowcart = new Snowcart();

    // Cover all aliases
    window.snowcart = snowcart;
    window.Snowcart = snowcart;
    window.SnowCart = snowcart;
})(window);
