/* globals window */

((Snowboard) => {
    class TestSingletonWithDependency extends Snowboard.Singleton {
        dependencies() {
            return ['testDependencyOne'];
        }

        listens() {
            return {
                ready: 'ready',
            };
        }

        ready() {
            return 'Ready';
        }

        testMethod() {
            return 'Tested';
        }
    }

    Snowboard.addPlugin('testSingleton', TestSingletonWithDependency);
})(window.Snowboard);
