import FakeDom from '../../helpers/FakeDom';

jest.setTimeout(5000);

describe('Form Widget dependsOn', function () {
    /**
     * Build a FakeDom with jQuery, WinterCMS foundation, stubs, and the form widget.
     * The FormWidgetStubs fixture provides minimal implementations of ocJSON,
     * $.fn.render, $.fn.request (synchronous success), and $.fn.loadIndicator
     */
    function buildDom(html) {
        return FakeDom
            .new()
            .addScript([
                'modules/backend/assets/js/vendor/jquery.min.js',
                'modules/system/assets/ui/js/foundation.baseclass.js',
                'modules/system/assets/ui/js/foundation.controlutils.js',
                'modules/system/tests/js/fixtures/formWidget/FormWidgetStubs.js',
                'modules/backend/widgets/form/assets/js/winter.form.js',
            ])
            .render(html);
    }

    /**
     * Build form HTML with fields and their dependsOn declarations.
     *
     * Collects all field names (both keys and referenced dependencies) and creates
     * a div for each. Fields that have dependencies get data-field-depends attributes.
     *
     * @param {Object} fields - Map of field names to arrays of field names they depend on.
     *                          e.g. { a: ['b'], b: ['a'] } for circular deps.
     */
    function buildFormHtml(fields) {
        // Collect all unique field names (both dependents and their dependencies)
        var allFields = {};
        for (var name in fields) {
            allFields[name] = fields[name];
            fields[name].forEach(function (dep) {
                if (!(dep in allFields)) {
                    allFields[dep] = null;
                }
            });
        }

        var fieldHtml = '';
        for (var fieldName in allFields) {
            fieldHtml += '<div data-field-name="' + fieldName + '"';
            if (allFields[fieldName] !== null) {
                fieldHtml += " data-field-depends='" + JSON.stringify(allFields[fieldName]) + "'";
            }
            fieldHtml += '><input type="text" name="' + fieldName + '"></div>';
        }

        return '<form>'
            + '<div data-control="formwidget" data-refresh-handler="onRefreshField">'
            + fieldHtml
            + '</div>'
            + '</form>';
    }

    it('refreshes dependent fields when a field changes', function (done) {
        buildDom(buildFormHtml({ fieldB: ['fieldA'] }))
            .then(function (dom) {
                var $ = dom.window.jQuery;
                var requestSpy = jest.spyOn($.fn, 'request');

                $('[data-field-name="fieldA"]').trigger('change');

                // The form widget debounces with a 300ms timer
                setTimeout(function () {
                    try {
                        expect(requestSpy).toHaveBeenCalledTimes(1);
                        expect(requestSpy).toHaveBeenCalledWith(
                            'onRefreshField',
                            expect.objectContaining({
                                data: expect.objectContaining({ fields: ['fieldB'] })
                            })
                        );
                        done();
                    } catch (e) {
                        done(e);
                    }
                }, 500);
            });
    });

    it('prevents infinite loop with circular dependsOn declarations', function (done) {
        buildDom(buildFormHtml({ fieldA: ['fieldB'], fieldB: ['fieldA'] }))
            .then(function (dom) {
                var $ = dom.window.jQuery;
                var requestSpy = jest.spyOn($.fn, 'request');

                $('[data-field-name="fieldA"]').trigger('change');

                // With 300ms debounce per step, an unguarded circular loop would fire
                // many times in 2 seconds. The fix should limit this to exactly 2
                // requests: A refreshes B, B refreshes A (blocked by cascade chain).
                setTimeout(function () {
                    try {
                        expect(requestSpy.mock.calls.length).toBe(2);
                        done();
                    } catch (e) {
                        done(e);
                    }
                }, 2000);
            });
    });

    it('allows transitive cascading (A -> B -> C) without blocking', function (done) {
        buildDom(buildFormHtml({ fieldB: ['fieldA'], fieldC: ['fieldB'] }))
            .then(function (dom) {
                var $ = dom.window.jQuery;
                var requestSpy = jest.spyOn($.fn, 'request');

                $('[data-field-name="fieldA"]').trigger('change');

                // A->B (300ms) then B->C (600ms). Allow time for both.
                setTimeout(function () {
                    try {
                        expect(requestSpy.mock.calls.length).toBe(2);
                        done();
                    } catch (e) {
                        done(e);
                    }
                }, 1500);
            });
    });

    it('stops cycle in a three-field circular chain (A -> B -> C -> A)', function (done) {
        buildDom(buildFormHtml({ fieldA: ['fieldC'], fieldB: ['fieldA'], fieldC: ['fieldB'] }))
            .then(function (dom) {
                var $ = dom.window.jQuery;
                var requestSpy = jest.spyOn($.fn, 'request');

                $('[data-field-name="fieldA"]').trigger('change');

                // A->B (300ms), B->C (600ms), C tries to refresh A but A is already
                // in the cascade chain [fieldA, fieldB] so it stops. Total: 3 requests.
                setTimeout(function () {
                    try {
                        expect(requestSpy.mock.calls.length).toBe(3);
                        done();
                    } catch (e) {
                        done(e);
                    }
                }, 2000);
            });
    });
});
