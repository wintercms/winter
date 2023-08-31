import Singleton from '../abstracts/Singleton';

/**
 * Sanitizer utility.
 *
 * Client-side HTML sanitizer designed mostly to prevent self-XSS attacks.
 * The sanitizer utility will strip all attributes that start with `on` (usually JS event handlers as attributes, i.e. `onload` or `onerror`) or contain the `javascript:` pseudo protocol in their values.
 *
 * @author Ben Thomson <git@alfreido.com>
 */
export default class Sanitizer extends Singleton {
    construct() {
        // Add to global function for backwards compatibility
        window.wnSanitize = (html) => this.sanitize(html);
        window.ocSanitize = window.wnSanitize;
    }

    sanitize(html, bodyOnly) {
        const parser = new DOMParser();
        const dom = parser.parseFromString(html, 'text/html');
        const returnBodyOnly = (bodyOnly !== undefined && typeof bodyOnly === 'boolean')
            ? bodyOnly
            : true;

        this.sanitizeNode(dom.getRootNode());

        return (returnBodyOnly) ? dom.body.innerHTML : dom.innerHTML;
    }

    sanitizeNode(node) {
        if (node.tagName === 'SCRIPT') {
            node.remove();
            return;
        }

        this.trimAttributes(node);

        const children = Array.from(node.children);

        children.forEach((child) => {
            this.sanitizeNode(child);
        });
    }

    trimAttributes(node) {
        if (!node.attributes) {
            return;
        }

        for (let i = 0; i < node.attributes.length; i += 1) {
            const attrName = node.attributes.item(i).name;
            const attrValue = node.attributes.item(i).value;

            /*
            * remove attributes where the names start with "on" (for example: onload, onerror...)
            * remove attributes where the value starts with the "javascript:" pseudo protocol (for example href="javascript:alert(1)")
            */
            /* eslint-disable-next-line */
            if (attrName.indexOf('on') === 0 || attrValue.indexOf('javascript:') === 0) {
                node.removeAttribute(attrName);
            }
        }
    }
}
