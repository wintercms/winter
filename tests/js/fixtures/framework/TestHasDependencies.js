/* globals window */

((Snowboard) => {
    class TestHasDependencies extends Snowboard.Singleton {
        dependencies() {
            return ['testDependencyOne', 'testDependencyTwo'];
        }

        testMethod() {
            return 'Tested';
        }
    }

    Snowboard.addPlugin('testHasDependencies', TestHasDependencies);
})(window.Snowboard);
