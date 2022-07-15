/* globals window */

((Snowboard) => {
    class DataConfigFixture extends Snowboard.PluginBase {
        construct(element) {
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
                base64: null,
            };
        }
    }

    Snowboard.addPlugin('dataConfigFixture', DataConfigFixture);
})(window.Snowboard);
