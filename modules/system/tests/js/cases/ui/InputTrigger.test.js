import FakeDom from '../../helpers/FakeDom';

jest.setTimeout(5000);

describe('Trigger API', function () {
    function buildDom(html) {
        return FakeDom
            .new()
            .addScript([
                'modules/backend/assets/js/vendor/jquery.min.js',
                'modules/system/tests/js/fixtures/formWidget/FormWidgetStubs.js',
                'modules/system/assets/ui/js/input.trigger.js',
            ])
            .render(html);
    }

    function afterPaint(window) {
        return new Promise(function (resolve) {
            window.requestAnimationFrame(function () {
                window.setTimeout(resolve);
            });
        });
    }

    function dependantHtml(name) {
        return '<div id="' + name + '" class="hide" data-trigger-action="show" data-trigger="#toggle" data-trigger-condition="checked"></div>';
    }

    it('resizes the window once after updating several dependants', function (done) {
        var html = '<input type="checkbox" id="toggle">'
            + ['first', 'second', 'third', 'fourth'].map(dependantHtml).join('');

        buildDom(html).then(function (dom) {
            var window = dom.window;
            var $ = window.jQuery;
            var resizes = 0;

            afterPaint(window).then(function () {
                $(window).on('resize', function () {
                    resizes++;
                });

                $('#toggle').prop('checked', true).trigger('change');

                expect($('.hide').length).toBe(0);
                expect(resizes).toBe(0);

                return afterPaint(window);
            }).then(function () {
                expect(resizes).toBe(1);
                done();
            }).catch(done);
        });
    });
});
