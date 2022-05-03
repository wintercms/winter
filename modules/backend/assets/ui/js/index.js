import BackendAjaxHandler from './ajax/Handler';
import AssetLoader from './ajax/AssetLoader';

if (window.Snowboard === undefined) {
    throw new Error('Snowboard must be loaded in order to use the Backend UI.');
}

((Snowboard) => {
    Snowboard.addPlugin('backend.ajax.handler', BackendAjaxHandler);
    Snowboard.addPlugin('backend.ajax.assetLoader', AssetLoader);
})(window.Snowboard);
