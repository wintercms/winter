/* globals window */

((Snowboard) => {
    class TestPlugin extends Snowboard.PluginBase {
        testMethod() {
            return 'Tested';
        }
    }

    Snowboard.addPlugin('testPlugin', TestPlugin);
})(window.Snowboard);
