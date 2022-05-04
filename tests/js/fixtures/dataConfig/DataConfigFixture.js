/* globals window */

((Snowboard) => {
    class DataConfigFixture extends Snowboard.PluginBase {
        constructor(snowboard, element) {
            super(snowboard);

            this.element = element;
            this.config = this.snowboard.dataConfig(this, element);
        }

        dependencies() {
            return ['dataConfig'];
        }

        defaults() {
            return {
                id: null,
                name: null,
                stringValue: null,
                boolean: null,
            };
        }
    }

    Snowboard.addPlugin('dataConfigFixture', DataConfigFixture);
})(window.Snowboard);
