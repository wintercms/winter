import FakeDom from '../../helpers/FakeDom';

jest.setTimeout(5000);

/*
 * The event log preview hands the stored log message to the exception beautifier.
 * {exception-beautifier-*} is the beautifier's own markup language -- buildMarkup() takes
 * the element name and any attributes straight out of a token -- and the formatters add
 * those tokens themselves, so a token that is already present in the message is not one of
 * the beautifier's own and must be rendered as the text it is.
 */
describe('Event log exception beautifier', function () {
    /*
     * The tab chrome needs the backend language store and the tab control, neither of
     * which is what these cases measure.
     */
    const STUBS = 'window.$.wn = window.$.wn || {};'
        + 'window.$.wn.lang = { get: function (key) { return key; } };'
        + 'window.$.fn.ocTab = function () { return this; };';

    /**
     * Renders a log message exactly as the preview does: the controller writes it into the
     * element as text, and the beautifier reads it back out with .html().
     */
    const beautify = (message) => FakeDom
        .new()
        .addScript([
            'modules/backend/assets/js/vendor/jquery.min.js',
            // Provides $.fn.render, which the beautifier uses to bind its auto-init.
            'modules/system/assets/js/framework.js',
            'modules/system/assets/js/eventlogs/exception-beautifier.js',
        ])
        .addInlineScript(STUBS)
        .render('<div id="log"></div>')
        .then((dom) => {
            const el = dom.window.document.getElementById('log');
            el.textContent = message;
            dom.window.jQuery(el).exceptionBeautifier();

            return { dom, el };
        });

    const attributeNames = (el) => {
        const names = [];
        el.querySelectorAll('*').forEach((node) => {
            Array.from(node.attributes).forEach((attribute) => names.push(attribute.name.toLowerCase()));
        });

        return names;
    };

    it('renders a token carried by the message as text', function () {
        const message = 'Something failed '
            + '{exception-beautifier-x#img src=q onerror=window.pwned=1}hi{/exception-beautifier-x#img}'
            + ' end';

        return beautify(message).then(({ el }) => {
            expect(el.querySelectorAll('img')).toHaveLength(0);
            expect(attributeNames(el).filter((name) => name.startsWith('on'))).toEqual([]);
            expect(el.textContent).toContain('{exception-beautifier-x#img src=q onerror=window.pwned=1}');
        });
    });

    it('renders the spacing tokens carried by the message as text', function () {
        const message = 'Line one {x-newline}not a break{x-tabulation}not a tab';

        return beautify(message).then(({ el }) => {
            expect(el.querySelectorAll('br')).toHaveLength(0);
            expect(el.textContent).toContain('{x-newline}');
            expect(el.textContent).toContain('{x-tabulation}');
        });
    });

    it('still beautifies an ordinary stack trace', function () {
        const message = 'Call to a member function get() on null\n'
            + 'Stack trace:\n'
            + '#0 /var/www/modules/system/ServiceProvider.php(42): System\\Classes\\Thing->boot()\n'
            + '#1 {main}';

        return beautify(message).then(({ el }) => {
            expect(el.querySelectorAll('.beautifier-message')).toHaveLength(1);
            expect(el.querySelectorAll('.beautifier-stacktrace-line').length).toBeGreaterThan(0);
            expect(el.querySelector('.beautifier-message').textContent)
                .toContain('Call to a member function get() on null');
            expect(el.querySelectorAll('.beautifier-file').length).toBeGreaterThan(0);
        });
    });

    it('displays literal braces in a message unchanged', function () {
        return beautify('Payload {"id": 4} was rejected {twice}').then(({ el }) => {
            expect(el.querySelector('.beautifier-message').textContent)
                .toContain('Payload {"id": 4} was rejected {twice}');
        });
    });

    it('sandboxes the logged mail body it previews', function () {
        const message = 'From: noreply@example.com\n'
            + 'To: someone@example.com\n'
            + 'Subject: Receipt\n'
            + 'Message-ID: <abc@localhost>\n'
            + '\n'
            + '<html><body><p>Hello</p></body></html>';

        return beautify(message).then(({ el }) => {
            const iframe = el.querySelector('iframe');

            expect(iframe).not.toBeNull();

            const tokens = (iframe.getAttribute('sandbox') || '').split(/\s+/).filter(Boolean);

            expect(tokens).not.toContain('allow-scripts');
            expect(tokens).not.toContain('allow-forms');
            expect(tokens).not.toContain('allow-top-navigation');
            expect(tokens).toContain('allow-same-origin');
        });
    });
});
