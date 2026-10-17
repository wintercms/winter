import FakeDom from '../../helpers/FakeDom';

jest.setTimeout(5000);

/*
 * The Mail branding preview renders the mail layout, the mail partials and the compiled
 * mail branding CSS into an iframe on a backend page, assigned through srcdoc. All three
 * are stored content and none of them needs to run script, so the frame is sandboxed.
 */
describe('Mail branding preview', function () {
    const build = () => FakeDom
        .new()
        .addScript([
            'modules/backend/assets/js/vendor/jquery.min.js',
            'modules/system/assets/js/framework.js',
            'modules/system/assets/js/mailbrandsettings/mailbrandsettings.js',
        ])
        .render('<div id="mailPreviewContainer"></div>');

    it('denies scripts in the preview frame', function (done) {
        build().then((dom) => {
            try {
                dom.window.createPreviewContainer(
                    dom.window.document.getElementById('mailPreviewContainer'),
                    '<img src=x onerror="window.top.__mailPreviewEscaped = true">'
                );

                const iframe = dom.window.document.getElementById('mailPreviewContainer');

                expect(iframe.tagName).toBe('IFRAME');
                expect(iframe.hasAttribute('sandbox')).toBe(true);

                const tokens = iframe.getAttribute('sandbox').split(/\s+/).filter(Boolean);

                expect(tokens).not.toContain('allow-scripts');
                expect(tokens).not.toContain('allow-top-navigation');
                expect(tokens).not.toContain('allow-forms');

                done();
            } catch (error) {
                done(error);
            }
        }).catch((error) => {
            done(error);
        });
    });

    /*
     * Invalidation counterpart: allow-same-origin has to stay, because
     * adjustPreviewHeight() reads the previewed document to size the frame. A sandbox
     * of the empty string would pass the assertions above while breaking the page.
     */
    it('keeps same-origin access so the frame can still be sized', function (done) {
        build().then((dom) => {
            try {
                dom.window.createPreviewContainer(
                    dom.window.document.getElementById('mailPreviewContainer'),
                    '<p>Hello</p>'
                );

                const iframe = dom.window.document.getElementById('mailPreviewContainer');

                expect(iframe.getAttribute('sandbox').split(/\s+/)).toContain('allow-same-origin');
                expect(iframe.srcdoc).toBe('<p>Hello</p>');

                done();
            } catch (error) {
                done(error);
            }
        }).catch((error) => {
            done(error);
        });
    });

    /*
     * Invalidation counterpart: the sample message's action button is a target="_blank"
     * link (modules/system/views/mail/partial-button.php), and a sandbox without these two
     * tokens silently stops it opening: the first is what allows a tab to be opened at all,
     * the second is what keeps the opened tab out of the sandbox so the linked page still
     * loads normally. Measured in Chromium, both are required for a click to behave as it
     * did before the frame was sandboxed.
     */
    it('lets a link in the previewed message still open its page', function (done) {
        build().then((dom) => {
            try {
                dom.window.createPreviewContainer(
                    dom.window.document.getElementById('mailPreviewContainer'),
                    '<a href="https://example.com/verify" target="_blank">Verify</a>'
                );

                const tokens = dom.window.document.getElementById('mailPreviewContainer')
                    .getAttribute('sandbox').split(/\s+/).filter(Boolean);

                expect(tokens).toContain('allow-popups');
                expect(tokens).toContain('allow-popups-to-escape-sandbox');

                done();
            } catch (error) {
                done(error);
            }
        }).catch((error) => {
            done(error);
        });
    });
});
