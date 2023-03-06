/* globals window */

((Snowboard) => {
    class TestClassWithTrait extends Snowboard.PluginBase {
        construct() {
            this.internalProperty = 'Internal property';
        }

        internalMethod() {
            return 'Internal method called';
        }

        traits() {
            return [
                TestTrait
            ];
        }
    }

    Snowboard.addPlugin('testClassWithTrait', TestClassWithTrait);
})(window.Snowboard);
