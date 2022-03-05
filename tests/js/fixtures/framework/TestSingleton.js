/* globals window */

((Snowboard) => {
    class TestSingleton extends Snowboard.Singleton {
        testMethod() {
            return 'Tested';
        }
    }

    Snowboard.addPlugin('test', TestSingleton);
})(window.Snowboard);
