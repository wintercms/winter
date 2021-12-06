import FakeDom from '../../../helpers/FakeDom';

describe('Winter framework', function () {
    it('initialises correctly', function (done) {
        FakeDom
            .new()
            .addScript('modules/system/assets/js/framework-next/build/framework.js')
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    try {
                        expect(dom.window.winter).toBeDefined();
                        expect(dom.window.winter.addModule).toBeDefined();
                        expect(dom.window.winter.addModule).toEqual(expect.any(Function));

                        // Check Module and Singleton abstracts exist
                        expect(dom.window.winter.Module).toBeDefined();
                        expect(dom.window.winter.Singleton).toBeDefined();

                        // Check in-built modules
                        expect(dom.window.winter.getModuleNames()).toEqual(
                            expect.arrayContaining(['debounce', 'jsonparser', 'sanitizer'])
                        );
                        expect(dom.window.winter.getModule('debounce').isFunction()).toEqual(true);
                        expect(dom.window.winter.getModule('debounce').isSingleton()).toEqual(false);
                        expect(dom.window.winter.getModule('jsonparser').isFunction()).toEqual(false);
                        expect(dom.window.winter.getModule('jsonparser').isSingleton()).toEqual(true);
                        expect(dom.window.winter.getModule('sanitizer').isFunction()).toEqual(false);
                        expect(dom.window.winter.getModule('sanitizer').isSingleton()).toEqual(true);

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

    it('can add and remove a module', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/framework-next/build/framework.js',
                'tests/js/fixtures/framework/TestModule.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const winter = dom.window.winter;

                    try {
                        // Check module caller
                        expect(winter.hasModule('test')).toBe(true);
                        expect(winter.getModuleNames()).toEqual(
                            expect.arrayContaining(['debounce', 'jsonparser', 'sanitizer', 'test'])
                        );
                        expect(winter.test).toEqual(expect.any(Function));

                        const instance = winter.test();

                        // Check module injected methods
                        expect(instance.winter).toBe(winter);
                        expect(instance.destructor).toEqual(expect.any(Function));

                        // Check module method
                        expect(instance.testMethod).toBeDefined();
                        expect(instance.testMethod).toEqual(expect.any(Function));
                        expect(instance.testMethod()).toEqual('Tested');

                        // Check multiple instances
                        const instanceOne = winter.test();
                        instanceOne.changed = true;
                        const instanceTwo = winter.test();
                        expect(instanceOne).not.toEqual(instanceTwo);
                        const factory = winter.getModule('test');
                        expect(factory.getInstances()).toEqual([instance, instanceOne, instanceTwo]);

                        // Remove module
                        winter.removeModule('test');
                        expect(winter.hasModule('test')).toEqual(false);
                        expect(dom.window.winter.getModuleNames()).toEqual(
                            expect.arrayContaining(['debounce', 'jsonparser', 'sanitizer'])
                        );
                        expect(winter.test).not.toBeDefined();

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
                'modules/system/assets/js/framework-next/build/framework.js',
                'tests/js/fixtures/framework/TestSingleton.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const winter = dom.window.winter;

                    try {
                         // Check module caller
                        expect(winter.hasModule('test')).toBe(true);
                        expect(winter.getModuleNames()).toEqual(
                            expect.arrayContaining(['debounce', 'jsonparser', 'sanitizer', 'test'])
                        );
                        expect(winter.test).toEqual(expect.any(Function));

                        const instance = winter.test();

                        // Check module injected methods
                        expect(instance.winter).toBe(winter);
                        expect(instance.destructor).toEqual(expect.any(Function));

                        // Check module method
                        expect(instance.testMethod).toBeDefined();
                        expect(instance.testMethod).toEqual(expect.any(Function));
                        expect(instance.testMethod()).toEqual('Tested');

                        // Check multiple instances  (these should all be the same as this instance is a singleton)
                        const instanceOne = winter.test();
                        instanceOne.changed = true;
                        const instanceTwo = winter.test();
                        expect(instanceOne).toEqual(instanceTwo);
                        const factory = winter.getModule('test');
                        expect(factory.getInstances()).toEqual([instance]);

                        // Remove module
                        winter.removeModule('test');
                        expect(winter.hasModule('test')).toEqual(false);
                        expect(dom.window.winter.getModuleNames()).toEqual(
                            expect.arrayContaining(['debounce', 'jsonparser', 'sanitizer'])
                        );
                        expect(winter.test).not.toBeDefined();

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
                'modules/system/assets/js/framework-next/build/framework.js',
                'tests/js/fixtures/framework/TestListener.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const winter = dom.window.winter;

                    try {
                        expect(winter.listensToEvent('eventOne')).toEqual(['test']);
                        expect(winter.listensToEvent('eventTwo')).toEqual(['test']);
                        expect(winter.listensToEvent('eventThree')).toEqual([]);

                        // Call global event one
                        const testClass = winter.test();
                        winter.globalEvent('eventOne', 42);
                        expect(testClass.eventResult).toEqual('Event called with arg 42');

                        // Call global event two - should fail as the test module doesn't have that method
                        expect(() => {
                            winter.globalEvent('eventTwo');
                        }).toThrow('Missing "notExists" method in "test" module');

                        // Call global event three - nothing should happen
                        expect(() => {
                            winter.globalEvent('eventThree');
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
                'modules/system/assets/js/framework-next/build/framework.js',
                'tests/js/fixtures/framework/TestPromiseListener.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const winter = dom.window.winter;

                    try {
                        expect(winter.listensToEvent('promiseOne')).toEqual(['test']);
                        expect(winter.listensToEvent('promiseTwo')).toEqual(['test']);
                        expect(winter.listensToEvent('promiseThree')).toEqual([]);

                        // Call global event one
                        const testClass = winter.test();
                        winter.globalPromiseEvent('promiseOne', 'promise').then(
                            () => {
                                expect(testClass.eventResult).toEqual('Event called with arg promise');

                                // Call global event two - it should still work, even though it doesn't return a promise
                                winter.globalPromiseEvent('promiseTwo', 'promise 2').then(
                                    () => {
                                        expect(testClass.eventResult).toEqual('Promise two called with arg promise 2');

                                        // Call global event three - it should still work
                                        winter.globalPromiseEvent('promiseThree', 'promise 3').then(
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
});
