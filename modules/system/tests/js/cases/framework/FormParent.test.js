import FakeDom from '../../helpers/FakeDom';

jest.setTimeout(2000);

describe('Original AJAX Framework library', function () {
    it('can get data from the current form and a series of parent forms', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/backend/assets/js/vendor/jquery.min.js',
                'modules/system/assets/js/framework.js',
            ])
            .render(
                '<form name="grandParentForm" id="grandParentForm" data-request-data="formOne: \'valueOne\'>' +
                    '<input name="fieldOne" value="valueOne" type="text">' +
                    '<input name="fieldFive" value="valueFive" type="text">' +
                    '<input name="multiField[]" value="multiOne" type="text">' +
                '</form>' +
                '<form name="parentForm" id="parentForm" data-request-parent="#grandParentForm" data-request-data="formTwo: \'valueTwo\'">' +
                    '<input name="fieldOne" value="overrideOne" type="text">' +
                    '<input name="fieldTwo" value="valueTwo" type="text">' +
                    '<input name="multiField[]" value="multiTwo" type="text">' +
                '</form>' +
                '<form name="childForm" id="childForm" data-request-parent="#parentForm" data-request="onTest" data-request-data="formThree: \'valueThree\'">' +
                    '<input name="fieldOne" value="overrideTwo" type="text">' +
                    '<input name="fieldThree" value="valueThree" type="text">' +
                    '<input name="fieldFour" value="valueFour" type="text">' +
                    '<input name="multiField[]" value="multiThree" type="text">' +
                    '<input name="multiFieldTwo[]" value="multiOne" type="text">' +
                    '<input name="multiFieldTwo[]" value="multiTwo" type="text">' +
                '</form>'
            )
            .then(
                (dom) => {
                    const parentDataSpy = jest.spyOn(dom.window.jQuery.fn, 'getRequestParentData');
                    jest.spyOn(dom.window, 'alert').mockImplementation(() => {});
                    jest.spyOn(dom.window.jQuery, 'ajax').mockImplementation(() => {
                        expect(parentDataSpy.mock.results[0].value).toMatchObject({
                            'formOne': 'valueOne',
                            'fieldOne': 'overrideTwo',
                            'fieldFive': 'valueFive',
                            'formTwo': 'valueTwo',
                            'fieldTwo': 'valueTwo',
                            'fieldThree': 'valueThree',
                            'fieldFour': 'valueFour',
                            'multiField[]': 'multiThree',
                            'multiFieldTwo[]': ['multiOne', 'multiTwo'],
                        });

                        done();
                        return dom.window.jQuery.Deferred();
                    });

                    dom.window.jQuery('#childForm').trigger('submit');
                }
            );
    });
});
