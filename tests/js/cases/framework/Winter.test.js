import { assert } from 'chai';
import FakeDom from '../../helpers/FakeDom';

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
                        assert.exists(dom.window.winter);
                        assert.exists(dom.window.winter.addModule);
                        assert.typeOf(dom.window.winter.addModule, 'function');

                        // Check Module and Singleton abstracts exist
                        assert.exists(dom.window.winter.Module);
                        assert.exists(dom.window.winter.Singleton);

                        // Check in-built modules
                        assert.deepEqual(['debounce', 'jsonparser', 'sanitizer'], dom.window.winter.getModuleNames());

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
                        assert.isTrue(winter.hasModule('test'))
                        assert.deepEqual(['debounce', 'jsonparser', 'sanitizer', 'test'], dom.window.winter.getModuleNames());
                        assert.isFunction(winter.test);
                        const instance = winter.test();

                        // Check module injected methods
                        assert.equal(winter, instance.winter);
                        assert.isFunction(instance.destructor);

                        // Check module method
                        assert.exists(instance.testMethod)
                        assert.isFunction(instance.testMethod)
                        assert.equal('Tested', instance.testMethod())

                        // Check multiple instances
                        const instanceOne = winter.test();
                        instanceOne.changed = true;
                        const instanceTwo = winter.test();
                        assert.notDeepEqual(instanceOne, instanceTwo);
                        const factory = winter.getModule('test');
                        assert.deepEqual([instance, instanceOne, instanceTwo], factory.getInstances());

                        // Remove module
                        winter.removeModule('test');
                        assert.isFalse(winter.hasModule('test'));
                        assert.deepEqual(['debounce', 'jsonparser', 'sanitizer'], dom.window.winter.getModuleNames());
                        assert.isUndefined(winter.test);

                        done()
                    } catch (error) {
                        done(error)
                    }
                },
                (error) => {
                    throw error
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
                        assert.isTrue(winter.hasModule('test'))
                        assert.deepEqual(['debounce', 'jsonparser', 'sanitizer', 'test'], dom.window.winter.getModuleNames());
                        assert.isFunction(winter.test);
                        const instance = winter.test();

                        // Check module injected methods
                        assert.equal(winter, instance.winter);
                        assert.isFunction(instance.destructor);

                        // Check module method
                        assert.exists(instance.testMethod)
                        assert.isFunction(instance.testMethod)
                        assert.equal('Tested', instance.testMethod())

                        // Check multiple instances (these should all be the same as this instance is a singleton)
                        const instanceOne = winter.test();
                        instanceOne.changed = true;
                        const instanceTwo = winter.test();
                        assert.deepEqual(instanceOne, instanceTwo);
                        const factory = winter.getModule('test');
                        assert.deepEqual([instance], factory.getInstances());

                        // Remove module
                        winter.removeModule('test');
                        assert.isFalse(winter.hasModule('test'));
                        assert.deepEqual(['debounce', 'jsonparser', 'sanitizer'], dom.window.winter.getModuleNames());
                        assert.isUndefined(winter.test);

                        done()
                    } catch (error) {
                        done(error)
                    }
                },
                (error) => {
                    throw error
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
                        assert.deepEqual(['test'], winter.listensToEvent('eventOne'));
                        assert.deepEqual(['test'], winter.listensToEvent('eventTwo'));
                        assert.deepEqual([], winter.listensToEvent('eventThree'));

                        // Call global event one
                        const testClass = winter.test();
                        winter.globalEvent('eventOne', 42);
                        assert.equal('Event called with arg 42', testClass.eventResult);

                        // Call global event two - should fail as the test module doesn't have that method
                        assert.throws(() => {
                            winter.globalEvent('eventTwo');
                        }, /Missing "notExists" method in "test" module/);

                        // Call global event three - nothing should happen
                        assert.doesNotThrow(() => {
                            winter.globalEvent('eventThree');
                        });

                        done()
                    } catch (error) {
                        done(error)
                    }
                },
                (error) => {
                    throw error
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
                        assert.deepEqual(['test'], winter.listensToEvent('promiseOne'));
                        assert.deepEqual(['test'], winter.listensToEvent('promiseTwo'));
                        assert.deepEqual([], winter.listensToEvent('promiseThree'));

                        // Call global event one
                        const testClass = winter.test();
                        winter.globalPromiseEvent('promiseOne', 'promise').then(
                            () => {
                                assert.equal('Event called with arg promise', testClass.eventResult);

                                // Call global event two - it should still work, even though it doesn't return a promise
                                winter.globalPromiseEvent('promiseTwo', 'promise 2').then(
                                    () => {
                                        assert.equal('Promise two called with arg promise 2', testClass.eventResult);

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
                        done(error)
                    }
                },
                (error) => {
                    throw error
                }
            );
    });
});
