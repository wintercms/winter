/**
 * Balloon Selector control.
 *
 * This control displays a series of options in a horizontal list and allows the selection of one item. The selected
 * item's value is then populated into a hidden input field to be used as the form data for this field.
 *
 * ### Usage:
 *
 *  ```html
 *  <div class="control-balloon-selector" data-control="balloon-selector">
 *      <ul>
 *          <li class="active" data-value="active">Active</li>
 *          <li class="" data-value="inactive">Inactive</li>
 *      </ul>
 *      <input name="field" type="hidden" value="active">
 *  </div>
 * ```
 *
 * @copyright 2022 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class BalloonSelector extends Snowboard.PluginBase {
    /**
     * Constructor.
     *
     * @param {Element} element
     */
    construct(element) {
        this.element = element;
        this.snowboard.disposable(this, element);

        this.input = element.querySelector('input[type="hidden"]');
        this.options = element.querySelectorAll('li');

        this.events = {
            click: (event) => this.onItemClick(event),
        };

        this.attachEvents();
    }

    /**
     * Destructor.
     */
    destruct() {
        this.detachEvents();
        this.element = null;
        this.input = null;
        this.options = null;

        super.destruct();
    }

    /**
     * Attaches click events to each option.
     */
    attachEvents() {
        this.options.forEach((option) => {
            option.addEventListener('click', this.events.click);
        });
    }

    /**
     * Detaches click events from each option.
     */
    detachEvents() {
        this.options.forEach((option) => {
            option.removeEventListener('click', this.events.click);
        });
    }

    /**
     * Item click handler.
     *
     * Makes the clicked item active, sets its value into the hidden field and triggers a "change" event. All other
     * items are made inactive.
     *
     * @param {Event} event
     * @returns {void}
     */
    onItemClick(event) {
        const target = event.target.closest('li');
        if (!target) {
            return;
        }

        this.options.forEach((option) => {
            option.classList.remove('active');
        });

        target.classList.add('active');
        this.input.value = target.dataset.value;
        const changeEvent = new Event('change', { bubbles: true, cancelable: true });
        this.input.dispatchEvent(changeEvent);
    }
}
