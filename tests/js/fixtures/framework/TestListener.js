/* globals window */

((winter) => {
    class TestListener extends winter.Singleton {
        listens() {
            return {
                eventOne: 'eventOne',
                eventTwo: 'notExists'
            };
        }

        eventOne(arg) {
            this.eventResult = 'Event called with arg ' + arg;
        }
    }

    winter.addModule('test', TestListener);
})(window.winter);
