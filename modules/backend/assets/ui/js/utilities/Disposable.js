/**
 * Disposable element plugin.
 *
 * This plugin is used to mark an element as disposable, and registers it to the Disposable observer. When this element
 * is removed, the attached plugin will have its destruct() method called.
 *
 * @copyright 2022 Winter.
 * @author Ben Thomson <git@alfreido.com>
 */
export default class Disposable extends Snowboard.PluginBase {
    construct(plugin, element) {
        this.plugin = plugin;
        this.element = element;

        element.dataset.disposable = true;
        this.snowboard.disposableObserver().registerDisposable(plugin, element);
    }
}
