import Winter from './main/Winter';

((window) => {
    const winter = new Winter(true);
    window.winter = winter;
    window.Winter = winter;
})(window);
