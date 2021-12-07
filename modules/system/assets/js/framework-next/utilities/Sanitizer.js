import Singleton from '../abstracts/Singleton';

export default class Sanitizer extends Singleton {
    constructor(winter) {
        super(winter);

        // Add to global function for backwards compatibility
        window.wnSanitize = (html) => {
            return this.sanitize(html);
        }
    }

    sanitize(html, bodyOnly) {
        const parser = new DOMParser()
        const dom = parser.parseFromString(html, 'text/html')
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

        for (let i = 0; i < node.attributes.length; i++) {
            const attrName = node.attributes.item(i).name;
            const attrValue = node.attributes.item(i).value;

            /*
            * remove attributes where the names start with "on" (for example: onload, onerror...)
            * remove attributes where the value starts with the "javascript:" pseudo protocol (for example href="javascript:alert(1)")
            */
            if (attrName.indexOf('on') === 0 || attrValue.indexOf('javascript:') === 0) {
                node.removeAttribute(attrName);
            }
        }
    }
}
