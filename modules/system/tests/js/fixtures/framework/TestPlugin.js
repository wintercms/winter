/* globals window */

((Snowboard) => {
    class TestPlugin extends Snowboard.PluginBase {
        testMethod() {
            return 'Tested';
        }
    }

    Snowboard.addPlugin('test', TestPlugin);
})(window.Snowboard);
