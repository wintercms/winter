import Overlay from './elements/Overlay';
import Tooltip from './elements/Tooltip';

if (window.Snowboard === undefined) {
    throw new Error('The Snowboard library must be loaded in order to use the Inspector widget')
}

((Snowboard) => {
    Snowboard.addPlugin('system.ui.overlay', Overlay);
    Snowboard.addPlugin('system.ui.tooltip', Tooltip);
})(window.Snowboard);
