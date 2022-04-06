import FakeDom from '../../../helpers/FakeDom';

describe('Snowboard framework', function () {
    it('initialises correctly', function (done) {
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
                    // Run assertions
                    try {
                        expect(dom.window.Snowboard).toBeDefined();
                        expect(dom.window.Snowboard.addPlugin).toBeDefined();
                        expect(dom.window.Snowboard.addPlugin).toEqual(expect.any(Function));

                        // Check PluginBase and Singleton abstracts exist
                        expect(dom.window.Snowboard.PluginBase).toBeDefined();
                        expect(dom.window.Snowboard.Singleton).toBeDefined();

                        // Check in-built plugins
                        expect(dom.window.Snowboard.getPluginNames()).toEqual(
                            expect.arrayContaining(['jsonparser', 'sanitizer'])
                        );
                        expect(dom.window.Snowboard.getPlugin('jsonparser').isFunction()).toEqual(false);
                        expect(dom.window.Snowboard.getPlugin('jsonparser').isSingleton()).toEqual(true);
                        expect(dom.window.Snowboard.getPlugin('sanitizer').isFunction()).toEqual(false);
                        expect(dom.window.Snowboard.getPlugin('sanitizer').isSingleton()).toEqual(true);

                        done();
                    } catch (error) {
                        done(error);
                    }
                },
                (error) => {
                    throw error;
                }
            );
    });

    it('can add and remove a plugin', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'tests/js/fixtures/framework/TestPlugin.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;

                    try {
                        // Check plugin caller
                        expect(Snowboard.hasPlugin('test')).toBe(true);
                        expect(Snowboard.getPluginNames()).toEqual(
                            expect.arrayContaining(['jsonparser', 'sanitizer', 'test'])
                        );
                        expect(Snowboard.test).toEqual(expect.any(Function));

                        const instance = Snowboard.test();

                        // Check plugin injected methods
                        expect(instance.snowboard).toBe(Snowboard);
                        expect(instance.destructor).toEqual(expect.any(Function));

                        // Check plugin method
                        expect(instance.testMethod).toBeDefined();
                        expect(instance.testMethod).toEqual(expect.any(Function));
                        expect(instance.testMethod()).toEqual('Tested');

                        // Check multiple instances
                        const instanceOne = Snowboard.test();
                        instanceOne.changed = true;
                        const instanceTwo = Snowboard.test();
                        expect(instanceOne).not.toEqual(instanceTwo);
                        const factory = Snowboard.getPlugin('test');
                        expect(factory.getInstances()).toEqual([instance, instanceOne, instanceTwo]);

                        // Remove plugin
                        Snowboard.removePlugin('test');
                        expect(Snowboard.hasPlugin('test')).toEqual(false);
                        expect(dom.window.Snowboard.getPluginNames()).toEqual(
                            expect.arrayContaining(['jsonparser', 'sanitizer'])
                        );
                        expect(Snowboard.test).not.toBeDefined();

                        done();
                    } catch (error) {
                        done(error);
                    }
                },
                (error) => {
                    throw error;
                }
            );
    });

    it('can add and remove a singleton', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'tests/js/fixtures/framework/TestSingleton.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;

                    try {
                         // Check plugin caller
                        expect(Snowboard.hasPlugin('test')).toBe(true);
                        expect(Snowboard.getPluginNames()).toEqual(
                            expect.arrayContaining(['jsonparser', 'sanitizer', 'test'])
                        );
                        expect(Snowboard.test).toEqual(expect.any(Function));

                        const instance = Snowboard.test();

                        // Check plugin injected methods
                        expect(instance.snowboard).toBe(Snowboard);
                        expect(instance.destructor).toEqual(expect.any(Function));

                        // Check plugin method
                        expect(instance.testMethod).toBeDefined();
                        expect(instance.testMethod).toEqual(expect.any(Function));
                        expect(instance.testMethod()).toEqual('Tested');

                        // Check multiple instances  (these should all be the same as this instance is a singleton)
                        const instanceOne = Snowboard.test();
                        instanceOne.changed = true;
                        const instanceTwo = Snowboard.test();
                        expect(instanceOne).toEqual(instanceTwo);
                        const factory = Snowboard.getPlugin('test');
                        expect(factory.getInstances()).toEqual([instance]);

                        // Remove plugin
                        Snowboard.removePlugin('test');
                        expect(Snowboard.hasPlugin('test')).toEqual(false);
                        expect(dom.window.Snowboard.getPluginNames()).toEqual(
                            expect.arrayContaining([ 'jsonparser', 'sanitizer'])
                        );
                        expect(Snowboard.test).not.toBeDefined();

                        done();
                    } catch (error) {
                        done(error);
                    }
                },
                (error) => {
                    throw error;
                }
            );
    });

    it('can listen and call global events', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'tests/js/fixtures/framework/TestListener.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;

                    try {
                        expect(Snowboard.listensToEvent('eventOne')).toEqual(['test']);
                        expect(Snowboard.listensToEvent('eventTwo')).toEqual(['test']);
                        expect(Snowboard.listensToEvent('eventThree')).toEqual([]);

                        // Call global event one
                        const testClass = Snowboard.test();
                        Snowboard.globalEvent('eventOne', 42);
                        expect(testClass.eventResult).toEqual('Event called with arg 42');

                        // Call global event two - should fail as the test plugin doesn't have that method
                        expect(() => {
                            Snowboard.globalEvent('eventTwo');
                        }).toThrow('Missing "notExists" method in "test" plugin');

                        // Call global event three - nothing should happen
                        expect(() => {
                            Snowboard.globalEvent('eventThree');
                        }).not.toThrow();

                        done();
                    } catch (error) {
                        done(error);
                    }
                },
                (error) => {
                    throw error;
                }
            );
    });

    it('can listen and call global promise events', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'tests/js/fixtures/framework/TestPromiseListener.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;

                    try {
                        expect(Snowboard.listensToEvent('promiseOne')).toEqual(['test']);
                        expect(Snowboard.listensToEvent('promiseTwo')).toEqual(['test']);
                        expect(Snowboard.listensToEvent('promiseThree')).toEqual([]);

                        // Call global event one
                        const testClass = Snowboard.test();
                        Snowboard.globalPromiseEvent('promiseOne', 'promise').then(
                            () => {
                                expect(testClass.eventResult).toEqual('Event called with arg promise');

                                // Call global event two - it should still work, even though it doesn't return a promise
                                Snowboard.globalPromiseEvent('promiseTwo', 'promise 2').then(
                                    () => {
                                        expect(testClass.eventResult).toEqual('Promise two called with arg promise 2');

                                        // Call global event three - it should still work
                                        Snowboard.globalPromiseEvent('promiseThree', 'promise 3').then(
                                            () => {
                                                done();
                                            },
                                            (error) => {
                                                done(error);
                                            }
                                        );
                                    },
                                    (error) => {
                                        done(error);
                                    }
                                );
                            },
                            (error) => {
                                done(error);
                            }
                        );
                    } catch (error) {
                        done(error);
                    }
                },
                (error) => {
                    throw error;
                }
            );
    });

    it('will throw an error when using a plugin that has unfulfilled dependencies', function () {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'tests/js/fixtures/framework/TestHasDependencies.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;

                    expect(() => {
                        Snowboard.testHasDependencies();
                    }).toThrow('The "testhasdependencies" plugin requires the following plugins: testdependencyone, testdependencytwo');
                },
                (error) => {
                    throw error;
                }
            );
    });

    it('will throw an error when using a plugin that has some unfulfilled dependencies', function () {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'tests/js/fixtures/framework/TestHasDependencies.js',
                'tests/js/fixtures/framework/TestDependencyOne.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;

                    expect(() => {
                        Snowboard.testHasDependencies();
                    }).toThrow('The "testhasdependencies" plugin requires the following plugins: testdependencytwo');
                },
                (error) => {
                    throw error;
                }
            );
    });

    it('will not throw an error when using a plugin that has fulfilled dependencies', function () {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'tests/js/fixtures/framework/TestDependencyTwo.js',
                'tests/js/fixtures/framework/TestHasDependencies.js',
                'tests/js/fixtures/framework/TestDependencyOne.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;

                    expect(() => {
                        Snowboard.testHasDependencies();
                    }).not.toThrow();

                    expect(Snowboard.testHasDependencies().testMethod()).toEqual('Tested');
                },
                (error) => {
                    throw error;
                }
            );
    });

    it('will not initialise a singleton that has unfulfilled dependencies', function () {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'tests/js/fixtures/framework/TestSingletonWithDependency.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;

                    expect(() => {
                        Snowboard.testSingleton();
                    }).toThrow('The "testsingleton" plugin requires the following plugins: testdependencyone');

                    expect(Snowboard.listensToEvent('ready')).not.toContain('testsingleton');

                    expect(() => {
                        Snowboard.globalEvent('ready');
                    }).not.toThrow();
                },
                (error) => {
                    throw error;
                }
            );
    });
});
