/* globals window */

((Snowboard) => {
    class TestDependencyOne extends Snowboard.Singleton {
        testMethod() {
            return 'Tested';
        }
    }

    Snowboard.addPlugin('testDependencyOne', TestDependencyOne);
})(window.Snowboard);
