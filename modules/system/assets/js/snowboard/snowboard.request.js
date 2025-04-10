import Request from './ajax/Request';

if (window.Snowboard === undefined) {
    throw new Error('Snowboard must be loaded in order to use the Javascript AJAX request feature.');
}

((Snowboard) => {
    Snowboard.addPlugin('request', Request);
})(window.Snowboard);
