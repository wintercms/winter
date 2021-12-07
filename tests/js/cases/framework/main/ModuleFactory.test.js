import FakeDom from '../../../helpers/FakeDom';

describe('Module factory', function () {
    it('can mock module methods', function (done) {
        FakeDom
            .new()
            .addScript('modules/system/assets/js/framework-next/build/framework.js')
            .render()
            .then(
                (dom) => {
                    dom.window.winter.getModule('sanitizer').mock('sanitize', () => {
                        return 'all good';
                    });

                    expect(
                        dom.window.winter.sanitizer().sanitize('<p onload="derp;"></p>')
                    ).toEqual('all good');

                    // Test unmock
                    dom.window.winter.getModule('sanitizer').unmock('sanitize');

                    expect(
                        dom.window.winter.sanitizer().sanitize('<p onload="derp;"></p>')
                    ).toEqual('<p></p>');

                    done();
                },
                (error) => {
                    done(error);
                }
            );
    });
});
