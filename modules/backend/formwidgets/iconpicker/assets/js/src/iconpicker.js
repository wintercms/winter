import { createApp } from 'vue';
import IconPickerVue from './components/IconPicker.vue';

((Snowboard) => {
    /**
     * Icon picker form widget.
     *
     * Creates an icon picker form widget, that contains a text field with the icon class, and a
     * popup that allows a user to easily select an icon.
     *
     * @author Robert Alexa <mail@robertalexa.me>
     * @copyright 2022 Winter CMS.
     */
    class IconPicker extends Snowboard.PluginBase {
        construct(element) {
            this.element = element;
            this.config = this.snowboard.dataConfig(this, element);

            this.events = {
                click: () => this.showPicker(),
            };
            this.iconPickerApp = null;

            this.input = element.querySelector('input');

            this.attachEvents();
        }

        defaults() {
            return {
                eventHandler: null,
            };
        }

        attachEvents() {
            this.element.querySelector('.input-group').addEventListener('click', this.events.click);
        }

        destruct() {
            if (this.iconPickerApp) {
                this.iconPickerApp.unmount();
                this.iconPickerApp = null;
            }

            this.element.querySelector('.input-group').removeEventListener('click', this.events.click);
            this.element = null;
        }

        showPicker() {
            this.snowboard.request(this.input, this.config.get('eventHandler'), {
                success: (data) => {
                    this.iconPickerApp = createApp(IconPickerVue, {
                        ...this.element.dataset,
                        fontLibraries: JSON.parse(data.result),
                    }).mount(this.element);

                    this.iconPickerApp.togglePicker();
                },
            });
        }
    }

    Snowboard.addPlugin('backend.formwidgets.iconpicker', IconPicker);
    Snowboard['backend.ui.widgetHandler']().register('iconpicker', 'backend.formwidgets.iconpicker');
})(window.Snowboard);
