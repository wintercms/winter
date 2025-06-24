import '../../less/relation.less';

((Snowboard) => {
    /**
     * Relation form widget.
     *
     * Renders a checkbox list field to select model related relations
     *
     * @author Damien MATHIEU <damsfx@gmail.com>
     * @copyright 2025 Winter CMS
     */
    class Relation extends Snowboard.PluginBase {
        /**
         * Constructor.
         *
         * @param {HTMLElement} element
         */
        construct(element) {
            this.element = element;
            this.config = this.snowboard.dataConfig(this, element);

            // Control elements
            this.expandAllControl = element.querySelector('[data-field-checkboxlist-expand-all]');
            this.collapseAllControl = element.querySelector('[data-field-checkboxlist-collapse-all]');
            this.expandCheckedControl = element.querySelector('[data-field-checkboxlist-expand-checked]');

            // Child elements
            this.items = element.querySelectorAll('.checkboxlist-item');
            this.toggles = element.querySelectorAll('.checkboxlist-item-toggle');

            // Events
            this.events = {
                expandAll: () => this.onExpandAll(),
                collapseAll: () => this.onCollapseAll(),
                expandChecked: () => this.onExpandChecked(),
                toggle: (el) => this.onToggle(el),
            };

            this.attachEvents();
        }

        /**
         * Sets the default options for this widget.
         *
         * @returns {Object}
         */
        defaults() {
            return {};
        }

        /**
         * Attaches event listeners for several interactions.
         */
        attachEvents() {
            this.expandAllControl.addEventListener('click', this.events.expandAll);
            this.collapseAllControl.addEventListener('click', this.events.collapseAll);
            this.expandCheckedControl.addEventListener('click', this.events.expandChecked);

            this.toggles.forEach((toggle) => {
                toggle.addEventListener('click', this.events.toggle)
            });
        }

        /**
         * Destructor.
         */
        destruct() {
            this.expandAllControl.removeEventListener('click', this.events.expandAll);
            this.collapseAllControl.removeEventListener('click', this.events.collapseAll);
            this.expandCheckedControl.removeEventListener('click', this.events.expandChecked);

            this.toggles.forEach((toggle) => {
                toggle.removeEventListener('click', this.events.toggle)
            });
        }

        /**
         * Open a single level of the tree
         *
         * @param {HTMLElement} el
         */
        openLevel(el) {
            el.classList.add('open');

            let child = el.querySelectorAll('.checkboxlist-children')[0];
            if (child) {
                child.classList.add('open');
            }
        }

        /**
         * Close an signle level of the tree
         *
         * @param {HTMLElement} el
         */
        closeLevel(el) {
            el.classList.remove('open');

            let child = el.querySelectorAll('.checkboxlist-children')[0];
            if (child) {
                child.classList.remove('open');
            }
        }

        /**
         * Expand all handler.
         *
         * Makes all nodes of the tree expanded.
         */
        onExpandAll() {
            this.items.forEach((item) => {
                this.openLevel(item);
            });

            this.updateScollBar();
        }

        /**
         * Collapse all handler.
         *
         * Makes all nodes of the tree collapsed.
         */
        onCollapseAll() {
            this.items.forEach((item) => {
                this.closeLevel(item);
            });

            this.updateScollBar();
        }

        /**
         * Expand checked handler.
         *
         * Makes all checked nodes of the tree expanded.
         */
        onExpandChecked() {
            this.onCollapseAll();

            let checked = Array.prototype.filter.call(this.items, function (level) {
                return level.matches(':has(input:checked)');
            });

            checked.forEach((item) => {
                this.openLevel(item);
            });

            this.updateScollBar();
        }

        /**
         * Toggle handler.
         *
         * Toggles a tree level expanded/collapsed.
         *
         * @param {HTMLElement} el
         */
        onToggle(el) {
            let parent = el.target.parentElement;

            if (parent.classList.contains('open')) {
                this.closeLevel(parent);
            } else {
                this.openLevel(parent);
            }

            this.updateScollBar();
        }

        /**
         * Update the sidebar height
         */
        updateScollBar() {
            // Update scrollbar height
            // Set a timer for .55s, waiting for the css animation to complete
            setTimeout(function (sidebar) {
                $('[data-control=scrollbar]').data('oc.scrollbar').update();
            }, 550);
        }
    }

    Snowboard.addPlugin('backend.formwidget.relation', Relation);
    Snowboard['backend.ui.widgethandler']().register('relation', 'backend.formwidget.relation');
})(window.Snowboard);
