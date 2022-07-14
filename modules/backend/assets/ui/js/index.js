import BackendAjaxHandler from './ajax/Handler';
import BackendUIHandler from './UIHandler';
import BalloonSelector from './controls/BalloonSelector';
import DateTimeControl from './controls/DateTime';
import DisposableObserver from './utilities/DisposableObserver';
import Disposable from './utilities/Disposable';

if (window.Snowboard === undefined) {
    throw new Error('Snowboard must be loaded in order to use the Backend UI.');
}

((Snowboard) => {
    // Utilities
    Snowboard.addPlugin('disposableObserver', DisposableObserver);
    Snowboard.addPlugin('disposable', Disposable);

    // AJAX plugins
    Snowboard.addPlugin('backend.ajax.handler', BackendAjaxHandler);

    // Controls
    Snowboard.addPlugin('backend.controls.balloonSelector', BalloonSelector);
    Snowboard.addPlugin('backend.controls.dateTime', DateTimeControl);

    // Main classes
    Snowboard.addPlugin('backend.ui.handler', BackendUIHandler);

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
