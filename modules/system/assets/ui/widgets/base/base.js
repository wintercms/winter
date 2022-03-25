import Overlay from './elements/Overlay';
import Tooltip from './elements/Tooltip';

if (window.Snowboard === undefined) {
    throw new Error('The Snowboard library must be loaded in order to use the Inspector widget')
}

((Snowboard) => {
    Snowboard.addPlugin('overlay', Overlay);
    Snowboard.addPlugin('tooltip', Tooltip);
})(window.Snowboard);
