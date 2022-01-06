/* globals window */

((winter) => {
    class TestModule extends winter.Module {
        testMethod() {
            return 'Tested';
        }
    }

    winter.addModule('test', TestModule);
})(window.winter);