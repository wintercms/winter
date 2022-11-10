import FakeDom from '../../../helpers/FakeDom';

describe('PluginLoader class', function () {
    it('can mock plugin methods', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js'
            ])
            .render()
            .then(
                (dom) => {
                    dom.window.Snowboard.getPlugin('sanitizer').mock('sanitize', () => {
                        return 'all good';
                    });

                    expect(
                        dom.window.Snowboard.sanitizer().sanitize('<p onload="derp;"></p>')
                    ).toEqual('all good');

                    // Test unmock
                    dom.window.Snowboard.getPlugin('sanitizer').unmock('sanitize');

                    expect(
                        dom.window.Snowboard.sanitizer().sanitize('<p onload="derp;"></p>')
                    ).toEqual('<p></p>');

                    done();
                },
                (error) => {
                    done(error);
                }
            );
    });

    it('is frozen on construction and doesn\'t allow prototype pollution', function () {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
            ])
            .render()
            .then(
                (dom) => {
                    const loader = dom.window.Snowboard.getPlugin('sanitizer');

                    expect(() => {
                        loader.newMethod = () => {
                            return true;
                        };
                    }).toThrow(TypeError);

                    expect(() => {
                        loader.newProperty = 'test';
                    }).toThrow(TypeError);

                    expect(() => {
                        loader.singleton.test = 'test';
                    }).toThrow(TypeError);

                    expect(loader.newMethod).toBeUndefined();
                    expect(loader.newProperty).toBeUndefined();
                },
                (error) => {
                    throw error;
                }
            );
    });

    it('should prevent modification of root instances', function () {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/tests/js/fixtures/framework/TestPlugin.js',
                'modules/system/tests/js/fixtures/framework/TestSingleton.js',
            ])
            .render()
            .then(
                (dom) => {
                    const rootInstance = dom.window.Snowboard.getPlugin('testPlugin').instance;

                    expect(() => {
                        rootInstance.newMethod = () => {
                            return true;
                        }
                    }).toThrow(TypeError);

                    expect(rootInstance.newMethod).toBeUndefined();

                    // Modifications can however be made to instances retrieved from the loader
                    const loadedInstance = dom.window.Snowboard.getPlugin('testPlugin').getInstance();

                    loadedInstance.newMethod = () => {
                        return true;
                    };
                    expect(loadedInstance.newMethod).toEqual(expect.any(Function));
                    expect(loadedInstance.newMethod()).toBe(true);

                    // But shouldn't follow through to new instances
                    const loadedInstanceTwo = dom.window.Snowboard.getPlugin('testPlugin').getInstance();
                    expect(loadedInstanceTwo.newMethod).toBeUndefined();

                    // The same rules apply for singletons, except that modifications will follow through to other uses
                    // of the singleton, since it's only one global instance.
                    const rootSingleton = dom.window.Snowboard.getPlugin('testSingleton').instance;

                    expect(() => {
                        rootSingleton.newMethod = () => {
                            return true;
                        }
                    }).toThrow(TypeError);

                    const loadedSingleton = dom.window.Snowboard.getPlugin('testSingleton').getInstance();

                    loadedSingleton.newMethod = () => {
                        return true;
                    };
                    expect(loadedSingleton.newMethod).toEqual(expect.any(Function));
                    expect(loadedSingleton.newMethod()).toBe(true);

                    const loadedSingletonTwo = dom.window.Snowboard.getPlugin('testSingleton').getInstance();
                    expect(loadedSingletonTwo.newMethod).toEqual(expect.any(Function));
                    expect(loadedSingletonTwo.newMethod()).toBe(true);
                }
            );
    });
});
