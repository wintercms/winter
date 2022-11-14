import Manager from './main/Manager';

if (window.Snowboard === undefined) {
    throw new Error('The Snowboard library must be loaded in order to use the Inspector widget');
}

((Snowboard) => {
    Snowboard.addPlugin('system.widgets.inspector', Manager);
})(window.Snowboard);
