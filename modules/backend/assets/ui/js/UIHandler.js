/**
 * UI Handler.
 *
 * @copyright 2022 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class UIHandler extends Snowboard.Singleton {
    dependencies() {
        return [
            'backend.controls.dateTime',
        ];
    }

    listens() {
        return {
            ready: 'ready',
            ajaxUpdate: 'ajaxUpdate',
        };
    }

    elementMatchers() {
        return {
            'backend.controls.dateTime': 'time[data-datetime-control]',
        };
    }

    ready() {
        Object.entries(this.elementMatchers()).forEach(([plugin, selector]) => {
            document.querySelectorAll(selector).forEach((element) => {
                this.snowboard[plugin](element);
            });
        });
    }

    ajaxUpdate(updatedElement) {
        Object.entries(this.elementMatchers()).forEach(([plugin, selector]) => {
            updatedElement.querySelectorAll(selector).forEach((element) => {
                this.snowboard[plugin](element);
            });
        });
    }
}
