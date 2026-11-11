import FakeDom from '../../helpers/FakeDom';

jest.setTimeout(5000);

describe('Repeater form widget', function () {
    function buildDom(html) {
        return FakeDom
            .new()
            .addScript([
                'modules/backend/assets/js/vendor/jquery.min.js',
                'modules/system/assets/ui/js/foundation.baseclass.js',
                'modules/system/assets/ui/js/foundation.controlutils.js',
                'modules/system/tests/js/fixtures/formWidget/FormWidgetStubs.js',
                'modules/backend/formwidgets/repeater/assets/js/repeater.js',
            ])
            .render(html);
    }

    function repeaterHtml(id, titleFrom, items) {
        return '<div id="' + id + '" class="field-repeater" data-control="fieldrepeater" data-title-from="' + titleFrom + '" data-style="collapsed">'
            + '<ul class="field-repeater-items">'
            + items.map(function (fields) {
                return '<li class="field-repeater-item">'
                    + '<div class="repeater-item-collapse"><a href="javascript:;" class="repeater-item-collapse-one"></a></div>'
                    + '<div class="repeater-item-collapsed-title"></div>'
                    + '<div class="field-repeater-form">' + fields + '</div>'
                    + '</li>';
            }).join('')
            + '</ul>'
            + '</div>';
    }

    function textField(name, value) {
        return '<div class="form-group" data-field-name="' + name + '"><input type="text" value="' + value + '"></div>';
    }

    it('keeps the titles of a nested repeater when its parent item collapses', function (done) {
        var nested = repeaterHtml('nested', 'alt', [
            textField('alt', 'First image'),
            textField('alt', 'Second image'),
        ]);
        var html = repeaterHtml('parent', 'title', [
            textField('title', 'Gallery') + '<div class="form-group">' + nested + '</div>',
        ]);

        buildDom(html).then(function (dom) {
            var $ = dom.window.jQuery;
            var $toggle = $('#parent > ul > li > .repeater-item-collapse .repeater-item-collapse-one');

            // Expand the parent item, then collapse it again
            $toggle.trigger('click');
            $toggle.trigger('click');

            try {
                expect($('#parent > ul > li').hasClass('collapsed')).toBe(true);
                expect($('#parent > ul > li > .repeater-item-collapsed-title').text()).toBe('Gallery');
                expect($('#nested > ul > li > .repeater-item-collapsed-title').map(function () {
                    return $(this).text();
                }).get()).toEqual(['First image', 'Second image']);
                done();
            } catch (e) {
                done(e);
            }
        });
    });
});
