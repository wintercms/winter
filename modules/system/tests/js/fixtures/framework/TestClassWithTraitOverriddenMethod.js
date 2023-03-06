/* globals window */

((Snowboard) => {
    class TestClassWithTraitOverriddenMethod extends Snowboard.PluginBase {
        construct() {
            this.internalProperty = 'Internal property';
            this.traitProperty = 'Overridden property';
        }

        testMethod() {
            return 'Overridden method called';
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

    Snowboard.addPlugin('testClassWithTraitOverriddenMethod', TestClassWithTraitOverriddenMethod);
})(window.Snowboard);
