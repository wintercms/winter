import Winter from './main/Winter';

((window) => {
    const winter = new Winter();
    window.winter = winter;
    window.Winter = winter;
})(window);
