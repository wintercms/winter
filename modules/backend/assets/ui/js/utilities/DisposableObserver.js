/**
 * Disposable observer.
 *
 * This plugin attaches a mutation observer to the entire document and watches for nodes being removed. When a node
 * is removed, it will find all disposable elements in the node and initiate their destruct methods.
 *
 * This is similar in concept to the original framework, but while the original framework was designed only to destruct
 * elements that are being removed due to an AJAX response, this will also work if the elements are removed in another
 * fashion, ie. through JavaScript.
 *
 * @copyright 2022 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class DisposableObserver extends Snowboard.Singleton {
    /**
     * Constructor.
     */
    construct() {
        this.observer = null;
        this.observed = [];

        this.events = {
            mutation: (event) => this.onMutation(event),
        };
    }

    /**
     * Listeners.
     *
     * @returns {Object}
     */
    listens() {
        return {
            ready: 'ready',
        };
    }

    /**
     * Ready handler.
     *
     * Creates the Mutation observer and attaches it to the document, observing any node changes only.
     */
    ready() {
        this.observer = new MutationObserver(this.events.mutation);
        this.observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    }

    /**
     * Registers an element as disposable.
     *
     * When this element is disposed, the attached plugin will have its destruct() method called.
     *
     * @param {Snowboard.PluginBase} plugin
     * @param {Element} element
     */
    registerDisposable(plugin, element) {
        this.observed.push({
            plugin,
            element,
        });
    }

    /**
     * Mutation handler.
     *
     * @param {MutationList} mutations
     */
    onMutation(mutations) {
        mutations.forEach((mutation) => {
            if (mutation.type !== 'childList') {
                return;
            }

            if (mutation.removedNodes.length === 0) {
                return;
            }

            mutation.removedNodes.forEach((removedNode) => {
                if (removedNode instanceof Element === false) {
                    return;
                }

                const disposableElements = removedNode.querySelectorAll('[data-disposable]');

                disposableElements.forEach((disposableElement) => {
                    const index = this.observed.findIndex((observed) => observed.element === disposableElement);
                    if (index === -1) {
                        return;
                    }

                    this.observed[index].plugin.destruct();
                    this.observed.splice(index, 1);
                })
            });
        });
    }
};
