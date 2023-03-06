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

    class TestExtensionOfClassWithTrait extends TestClassWithTrait {
        extendedMethod() {
            return 'Extension method called';
        }

        testMethodTwo() {
            return 'Overridden method called';
        }

        traits() {
            return [
                TestTraitTwo,
            ];
        }
    }

    Snowboard.addPlugin('testExtensionOfClassWithTrait', TestExtensionOfClassWithTrait);
})(window.Snowboard);
