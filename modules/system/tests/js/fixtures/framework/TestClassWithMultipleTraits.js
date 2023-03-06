/* globals window */

((Snowboard) => {
    class TestClassWithMultipleTraits extends Snowboard.PluginBase {
        construct() {
            this.internalProperty = 'Internal property';
        }

        internalMethod() {
            return 'Internal method called';
        }

        traits() {
            return [
                TestTrait,
                TestTraitTwo,
            ];
        }
    }

    Snowboard.addPlugin('testClassWithMultipleTraits', TestClassWithMultipleTraits);
})(window.Snowboard);
