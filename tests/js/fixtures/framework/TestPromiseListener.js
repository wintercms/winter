/* globals window */

((winter) => {
    class TestPromiseListener extends winter.Singleton {
        listens() {
            return {
                promiseOne: 'promiseOne',
                promiseTwo: 'promiseTwo'
            };
        }

        promiseOne(arg) {
            return new Promise((resolve) => {
                window.setTimeout(() => {
                    this.eventResult = 'Event called with arg ' + arg;
                    resolve();
                }, 500);
            });
        }

        promiseTwo(arg) {
            this.eventResult = 'Promise two called with arg ' + arg;
            return true;
        }
    }

    winter.addModule('test', TestPromiseListener);
})(window.winter);
