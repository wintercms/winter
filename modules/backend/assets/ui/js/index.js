import BackendAjaxHandler from './ajax/Handler';

if (window.Snowboard === undefined) {
    throw new Error('Snowboard must be loaded in order to use the Backend UI.');
}

((Snowboard) => {
    Snowboard.addPlugin('backend.ajax.handler', BackendAjaxHandler);
    // Add the pre-filter immediately
    Snowboard['backend.ajax.handler']().addPrefilter();

    // Add polyfill for AssetManager
    window.AssetManager = {
        load: (assets, callback) => {
            Snowboard.assetLoader().load(assets).then(
                () => {
                    if (callback && typeof callback === 'function') {
                        callback();
                    }
                },
            );
        },
    };
    window.assetManager = window.AssetManager;
})(window.Snowboard);
