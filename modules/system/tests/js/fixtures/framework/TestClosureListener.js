/* globals window */

((Snowboard) => {
    class TestClosureListener extends Snowboard.Singleton {
        listens() {
            return {
                eventOne: () => {
                    this.eventResult = 'Closure eventOne called';
                },
                eventTwo: (arg) => {
                    this.eventResult = `Closure eventTwo called with arg '${arg}'`;
                }
            };
        }
    }

    Snowboard.addPlugin('testClosure', TestClosureListener);
})(window.Snowboard);
