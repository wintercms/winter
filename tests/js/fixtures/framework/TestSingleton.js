/* globals window */

((winter) => {
    class TestSingleton extends winter.Singleton {
        testMethod() {
            return 'Tested';
        }
    }

    winter.addModule('test', TestSingleton);
})(window.winter);
