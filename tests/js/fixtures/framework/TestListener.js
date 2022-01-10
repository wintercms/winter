/* globals window */

((Snowboard) => {
    class TestListener extends Snowboard.Singleton {
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

    Snowboard.addPlugin('test', TestListener);
})(window.Snowboard);
