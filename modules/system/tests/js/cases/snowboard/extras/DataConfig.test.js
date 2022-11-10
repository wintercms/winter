import FakeDom from '../../../helpers/FakeDom';

jest.setTimeout(2000);

describe('The Data Config extra functionality', function () {
    it('can read the config from an element\'s data attributes', function (done) {
        FakeDom
            .new()
            .addCss([
                'modules/system/assets/css/snowboard.extras.css',
            ])
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.extras.js',
                'modules/system/tests/js/fixtures/dataConfig/DataConfigFixture.js',
            ])
            .render(
                `<div
                    id="testElement"
                    data-id="389"
                    data-string-value="Hi there"
                    data-boolean="true"
                ></div>

                <div
                    id="testElementTwo"
                    data-string-value="Hi there again"
                    data-name="Ben"
                    data-boolean="false"
                    data-extra-attr="This should not be available"
                    data-base64="base64:SSdtIGEgQmFzZTY0LWRlY29kZWQgc3RyaW5n"
                ></div>`
            )
            .then(
                (dom) => {
                    const instance = dom.window.Snowboard.dataConfigFixture(
                        dom.window.document.querySelector('#testElement')
                    );

                    try {
                        expect(instance.config.get('id')).toEqual(389);
                        // Name should be null as it's the default value and not specified above
                        expect(instance.config.get('name')).toBeNull();
                        expect(instance.config.get('stringValue')).toBe('Hi there');
                        // Missing should be undefined as it's neither defined nor part of the default data
                        expect(instance.config.get('missing')).toBeUndefined();
                        expect(instance.config.get('boolean')).toBe(true);

                        expect(instance.config.get()).toMatchObject({
                            id: 389,
                            name: null,
                            stringValue: 'Hi there',
                            boolean: true,
                            base64: null,
                        });
                    } catch (error) {
                        done(error);
                        return;
                    }

                    const instanceTwo = dom.window.Snowboard.dataConfigFixture(
                        dom.window.document.querySelector('#testElementTwo')
                    );

                    try {
                        // ID is null as it's the default value and not specified above
                        expect(instanceTwo.config.get('id')).toBeNull();
                        expect(instanceTwo.config.get('name')).toBe('Ben');
                        expect(instanceTwo.config.get('stringValue')).toBe('Hi there again');
                        expect(instanceTwo.config.get('missing')).toBeUndefined();
                        expect(instanceTwo.config.get('boolean')).toBe(false);
                        // Extra attr is specified above, but it should not be available as a config value
                        // because it's not part of the `defaults()` in the fixture
                        expect(instanceTwo.config.get('extraAttr')).toBeUndefined();
                        // Base-64 decoded string
                        expect(instanceTwo.config.get('base64')).toBe('I\'m a Base64-decoded string');

                        done();
                    } catch (error) {
                        done(error);
                    }
                }
            );
    });

    it('can read the config from every data attribute of an element with "acceptAllDataConfigs" enabled', function (done) {
        FakeDom
            .new()
            .addCss([
                'modules/system/assets/css/snowboard.extras.css',
            ])
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.extras.js',
                'modules/system/tests/js/fixtures/dataConfig/DataConfigFixture.js',
            ])
            .render(
                `<div
                    id="testElementTwo"
                    data-string-value="Hi there again"
                    data-name="Ben"
                    data-boolean="false"
                    data-extra-attr="This should now be available"
                    data-json="{ &quot;name&quot;: &quot;Ben&quot; }"
                    data-another-base64="base64:dHJ1ZQ=="
                    data-json-base64="base64:eyAiaWQiOiAxLCAidGl0bGUiOiAiU29tZSB0aXRsZSIgfQ=="
                ></div>`
            )
            .then(
                (dom) => {
                    const instance = dom.window.Snowboard.dataConfigFixture(
                        dom.window.document.querySelector('#testElementTwo')
                    );
                    instance.acceptAllDataConfigs = true;
                    instance.config.refresh();

                    try {
                        // ID is null as it's the default value and not specified above
                        expect(instance.config.get('id')).toBeNull();
                        expect(instance.config.get('name')).toBe('Ben');
                        expect(instance.config.get('stringValue')).toBe('Hi there again');
                        expect(instance.config.get('missing')).toBeUndefined();
                        expect(instance.config.get('boolean')).toBe(false);
                        // These attributes below are specified above, and although they're not part of the
                        // defaults, they should be available because "acceptAllDataConfigs" is true
                        expect(instance.config.get('extraAttr')).toBe('This should now be available');
                        expect(instance.config.get('json')).toMatchObject({
                            name: 'Ben'
                        });
                        expect(instance.config.get('anotherBase64')).toBe(true);
                        expect(instance.config.get('jsonBase64')).toMatchObject({
                            id: 1,
                            title: 'Some title',
                        });

                        expect(instance.config.get()).toMatchObject({
                            id: null,
                            name: 'Ben',
                            stringValue: 'Hi there again',
                            boolean: false,
                            extraAttr: 'This should now be available',
                            json: {
                                name: 'Ben',
                            },
                            anotherBase64: true,
                            jsonBase64: {
                                id: 1,
                                title: 'Some title',
                            },
                        });

                        done();
                    } catch (error) {
                        done(error);
                    }
                }
            );
    });

    it('can refresh the config from the data attributes on the fly', function (done) {
        FakeDom
            .new()
            .addCss([
                'modules/system/assets/css/snowboard.extras.css',
            ])
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.extras.js',
                'modules/system/tests/js/fixtures/dataConfig/DataConfigFixture.js',
            ])
            .render(
                `<div
                    id="testElement"
                    data-string-value="Hi there again"
                    data-name="Ben"
                    data-boolean="no"
                ></div>`
            )
            .then(
                (dom) => {
                    const instance = dom.window.Snowboard.dataConfigFixture(
                        dom.window.document.querySelector('#testElement')
                    );

                    try {
                        expect(instance.config.get('id')).toBeNull();
                        expect(instance.config.get('name')).toBe('Ben');
                        expect(instance.config.get('stringValue')).toBe('Hi there again');
                        expect(instance.config.get('boolean')).toBe(false);

                        expect(instance.config.get()).toMatchObject({
                            id: null,
                            name: 'Ben',
                            stringValue: 'Hi there again',
                            boolean: false,
                        });

                        dom.window.document.querySelector('#testElement').setAttribute('data-id', '456');
                        dom.window.document.querySelector('#testElement').setAttribute('data-string-value', 'Changed');
                        dom.window.document.querySelector('#testElement').removeAttribute('data-boolean');

                        // Refresh config
                        instance.config.refresh();

                        expect(instance.config.get('id')).toBe(456);
                        expect(instance.config.get('name')).toBe('Ben');
                        expect(instance.config.get('stringValue')).toBe('Changed');
                        expect(instance.config.get('boolean')).toBeNull();

                        expect(instance.config.get()).toMatchObject({
                            id: 456,
                            name: 'Ben',
                            stringValue: 'Changed',
                            boolean: null,
                        });

                        done();
                    } catch (error) {
                        done(error);
                    }
                }
            );
    });

    it('can set config values at runtime', function (done) {
        FakeDom
            .new()
            .addCss([
                'modules/system/assets/css/snowboard.extras.css',
            ])
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.extras.js',
                'modules/system/tests/js/fixtures/dataConfig/DataConfigFixture.js',
            ])
            .render(
                `<div
                    id="testElement"
                    data-string-value="Hi there again"
                    data-name="Ben"
                    data-boolean="false"
                ></div>`
            )
            .then(
                (dom) => {
                    const instance = dom.window.Snowboard.dataConfigFixture(
                        dom.window.document.querySelector('#testElement')
                    );

                    try {
                        expect(instance.config.get('name')).toBe('Ben');
                        // Set config
                        instance.config.set('name', 'Luke');
                        expect(instance.config.get('name')).toBe('Luke');
                        // Refresh config
                        instance.config.refresh();
                        expect(instance.config.get('name')).toBe('Ben');
                        done();
                    } catch (error) {
                        done(error);
                    }
                }
            );
    });

    it('can set config values at runtime that persist through a reset', function (done) {
        FakeDom
            .new()
            .addCss([
                'modules/system/assets/css/snowboard.extras.css',
            ])
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.extras.js',
                'modules/system/tests/js/fixtures/dataConfig/DataConfigFixture.js',
            ])
            .render(
                `<div
                    id="testElement"
                    data-string-value="Hi there again"
                    data-name="Ben"
                    data-boolean="no"
                ></div>`
            )
            .then(
                (dom) => {
                    const instance = dom.window.Snowboard.dataConfigFixture(
                        dom.window.document.querySelector('#testElement')
                    );

                    try {
                        expect(instance.config.get('name')).toBe('Ben');
                        // Set config
                        instance.config.set('name', 'Luke', true);
                        expect(instance.config.get('name')).toBe('Luke');
                        // Refresh config
                        instance.config.refresh();
                        expect(instance.config.get('name')).toBe('Luke');
                        done();
                    } catch (error) {
                        done(error);
                    }
                }
            );
    });
});
