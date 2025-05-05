import FakeDom from '../../../helpers/FakeDom';

jest.setTimeout(2000);

describe('Data Attribute Request AJAX library', function () {
    it('can parse request data', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js',
                'modules/system/assets/js/snowboard/build/snowboard.data-attr.js',
            ])
            .render()
            .then(
                (dom) => {
                    const DataAttributeSingleton = dom.window.Snowboard.attributeRequest();

                    expect(
                        DataAttributeSingleton.parseData('{foo: "bar"}')
                    ).toEqual({ foo: 'bar' });

                    expect(
                        DataAttributeSingleton.parseData('foo: \'bar\'')
                    ).toEqual({ foo: 'bar' });

                    expect(
                        DataAttributeSingleton.parseData('{"key": "value", "nested": { "otherKey": "otherValue" }}')
                    ).toEqual({
                        "key": "value",
                        "nested": {
                            "otherKey": "otherValue"
                        }
                    });

                    done();
                }
            );
    });
});
