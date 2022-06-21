/* globals __dirname, URL */

import { JSDOM } from 'jsdom'
import path from 'path'

export default class FakeDom
{
    constructor(content, options)
    {
        if (options === undefined) {
            options = {};
        }

        // Header settings
        this.url = options.url || `file://${path.resolve(__dirname, '../../../../')}`;
        this.referer = options.referer;
        this.contentType = options.contentType || 'text/html';

        // Content settings
        this.headStart = options.headStart || '<!DOCTYPE html><html><head><title>Fake document</title>';
        this.headEnd = options.headEnd || '</head>';
        this.bodyStart = options.bodyStart || '<body>';
        this.content = content || '';
        this.bodyEnd = options.bodyEnd || '</body>';
        this.foot = options.foot || '</html>';

        // Callback settings
        this.beforeParse = (typeof options.beforeParse === 'function')
            ? options.beforeParse
            : undefined;

        // Assets
        this.css = [];
        this.scripts = [];
        this.inline = [];
    }

    static new(content, options)
    {
        return new FakeDom(content, options);
    }

    setContent(content)
    {
        this.content = content;
        return this;
    }

    addScript(script, id)
    {
        if (Array.isArray(script)) {
            script.forEach((item) => {
                this.addScript(item);
            });

            return this;
        }

        let url = new URL(script, this.url);
        let base = new URL(this.url);

        if (url.host === base.host) {
            this.scripts.push({
                url: `${url.pathname}`,
                id: id || this.generateId(),
            });
        } else {
            this.scripts.push({
                url,
                id: id || this.generateId(),
            });
        }

        return this;
    }

    addCss(css, id)
    {
        if (Array.isArray(css)) {
            css.forEach((item) => {
                this.addCss(item)
            });

            return this;
        }

        let url = new URL(css, this.url);
        let base = new URL(this.url);

        if (url.host === base.host) {
            this.css.push({
                url: `${url.pathname}`,
                id: id || this.generateId(),
            });
        } else {
            this.css.push({
                url,
                id: id || this.generateId(),
            });
        }

        return this;
    }

    addInlineScript(script, id)
    {
        this.inline.push({
            script,
            id: id || this.generateId(),
            element: null,
        });

        return this;
    }

    generateId()
    {
        let id = 'script-';
        let chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-';
        let charLength = chars.length;

        for (let i = 0; i < 10; i++) {
            let currentChar = chars.substr(Math.floor(Math.random() * charLength), 1);
            id = `${id}${currentChar}`;
        }

        return id;
    }

    render(content)
    {
        if (content) {
            this.content = content;
        }
        return new Promise((resolve, reject) => {
            try {
                const dom = new JSDOM(
                    this._renderContent(),
                    {
                        url: this.url,
                        referrer: this.referer,
                        contentType: this.contentType,
                        includeNodeLocations: true,
                        runScripts: 'dangerously',
                        resources: 'usable',
                        pretendToBeVisual: true,
                        beforeParse: this.beforeParse,
                    }
                );

                dom.window.resolver = () => {
                    resolve(dom);
                };
            } catch (e) {
                reject(e);
            }
        });
    }

    _renderContent()
    {
        // Create content list
        const content = [this.headStart];

        // Embed CSS
        this.css.forEach((css) => {
            content.push(`<link rel="stylesheet" href="${css.url}" id="${css.id}">`);
        });

        content.push(this.headEnd, this.bodyStart, this.content);

        // Embed scripts
        this.scripts.forEach((script) => {
            content.push(`<script src="${script.url}" id="${script.id}"></script>`);
        });
        this.inline.forEach((script) => {
            content.push(`<script id="${script.id}">${script.script}</script>`);
        });

        // Add resolver
        content.push(`<script>window.resolver()</script>`);

        // Add final content
        content.push(this.bodyEnd);
        content.push(this.foot);

        return content.join('\n');
    }
}
