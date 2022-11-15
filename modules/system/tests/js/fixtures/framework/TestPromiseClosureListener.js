/* globals window */

((Snowboard) => {
    class TestPromiseClosureListener extends Snowboard.Singleton {
        listens() {
            return {
                promiseOne: (arg) => {
                    return new Promise((resolve) => {
                        window.setTimeout(() => {
                            this.eventResult = 'Event called with arg ' + arg;
                            resolve();
                        }, 500);
                    });
                },
                promiseTwo: (arg) => {
                    this.eventResult = 'Promise two called with arg ' + arg;
                    return true;
                },
            };
        }
    }

    Snowboard.addPlugin('testClosure', TestPromiseClosureListener);
})(window.Snowboard);
