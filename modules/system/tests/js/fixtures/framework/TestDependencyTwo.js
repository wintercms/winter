/* globals window */

((Snowboard) => {
    class TestDependencyTwo extends Snowboard.Singleton {
        testMethod() {
            return 'Tested';
        }
    }

    Snowboard.addPlugin('testDependencyTwo', TestDependencyTwo);
})(window.Snowboard);
