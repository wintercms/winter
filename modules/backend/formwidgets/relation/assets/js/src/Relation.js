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
            if (this.expandAllControl) {
                this.expandAllControl.addEventListener('click', this.events.expandAll);
            }
            if (this.collapseAllControl) {
                this.collapseAllControl.addEventListener('click', this.events.collapseAll);
            }
            if (this.expandCheckedControl) {
                this.expandCheckedControl.addEventListener('click', this.events.expandChecked);
            }

            this.toggles.forEach((toggle) => {
                toggle.addEventListener('click', this.events.toggle)
            });
        }

        /**
         * Destructor.
         */
        destruct() {
            if (this.expandAllControl) {
                this.expandAllControl.removeEventListener('click', this.events.expandAll);
            }
            if (this.collapseAllControl) {
                this.collapseAllControl.removeEventListener('click', this.events.collapseAll);
            }
            if (this.expandCheckedControl) {
                this.expandCheckedControl.removeEventListener('click', this.events.expandChecked);
            }

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
            const openPromise = new Promise((resolve, reject) => {
                let animatedNodes = this.getExpandableNodes();

                animatedNodes.forEach((item) => {
                    this.openLevel(item);
                });

                resolve([].slice.call(animatedNodes).pop());
            });

            openPromise.then((el) => {
                this.updateScrollBar(el);
            });
        }

        /**
         * Collapse all handler.
         *
         * Makes all nodes of the tree collapsed.
         */
        onCollapseAll() {
            const closePromise = new Promise((resolve, reject) => {
                let animatedNodes = this.getOpenedNodes();

                animatedNodes.forEach((item) => {
                    this.closeLevel(item);
                });

                resolve([].slice.call(animatedNodes).pop());
            });

            closePromise.then((el) => {
                this.updateScrollBar(el);
            });
        }

        /**
         * Expand checked handler.
         *
         * Makes all checked nodes of the tree expanded.
         */
        onExpandChecked() {
            this.onCollapseAll();

            const selectedPromise = new Promise((resolve, reject) => {
                let animatedNodes = this.getCheckedNodes();

                animatedNodes.forEach((item) => {
                    this.openLevel(item);
                });

                resolve([].slice.call(animatedNodes).pop());
            });

            selectedPromise.then((el) => {
                this.updateScrollBar(el);
            });
        }

        /**
         * Toggle handler.
         *
         * Toggles a tree level expanded/collapsed.
         *
         * @param {HTMLElement} el
         */
        onToggle(el) {
            const tooglePromise = new Promise((resolve, reject) => {
                let parent = el.target.parentElement;

                if (parent.classList.contains('open')) {
                    this.closeLevel(parent);
                } else {
                    this.openLevel(parent);
                }

                resolve(parent);
            });

            tooglePromise.then((parent) => {
                this.updateScrollBar(parent);
            });
        }

        /**
         * Update the sidebar height
         *
         * @param {HTMLElement} el The last animated node of the tree
         */
        updateScrollBar(el) {
            if (el === undefined) {
                return;
            }

            let openedLevel = el.classList.contains("checkboxlist-children") ? el : el.querySelector('.checkboxlist-children');

            if (!openedLevel) {
                return;
            }

            openedLevel.addEventListener("transitionend", () => {
                $('[data-control=scrollbar]').data('oc.scrollbar').update();
            }, {once: true});
        }

        /**
         * Filter treeview nodes to get only those who have childs
         *
         * @returns {Array}
         */
        getExpandableNodes() {
            return Array.prototype.filter.call(this.items, function (level) {
                return level.matches(':has(.checkboxlist-children)');
            });
        }

        /**
         * Filter treeview nodes to get only opened ones
         *
         * @returns {Array}
         */
        getOpenedNodes() {
            return Array.prototype.filter.call(this.items, function (level) {
                return level.classList.contains("open")
            });
        }

        /**
         * Filter treeview nodes to get only those containing checked checkboxes
         *
         * @returns {Array}
         */
        getCheckedNodes() {
            return Array.prototype.filter.call(this.getExpandableNodes(), function (level) {
                return level.matches(':has(input:checked)');
            });
        }
    }

    Snowboard.addPlugin('backend.formwidget.relation', Relation);
    Snowboard['backend.ui.widgethandler']().register('relation', 'backend.formwidget.relation');
})(window.Snowboard);
