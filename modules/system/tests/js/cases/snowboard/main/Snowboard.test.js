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

    it('is frozen on construction and doesn\'t allow prototype pollution', function () {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/tests/js/fixtures/framework/TestPlugin.js',
            ])
            .render()
            .then(
                (dom) => {
                    expect(() => {
                        dom.window.Snowboard.newMethod = () => {
                            return true;
                        };
                    }).toThrow(TypeError);

                    expect(() => {
                        dom.window.Snowboard.newProperty = 'test';
                    }).toThrow(TypeError);

                    expect(() => {
                        dom.window.Snowboard.readiness.test = 'test';
                    }).toThrow(TypeError);

                    expect(dom.window.Snowboard.newMethod).toBeUndefined();
                    expect(dom.window.Snowboard.newProperty).toBeUndefined();

                    // You should not be able to modify the Snowboard object fed to plugins either
                    const instance = dom.window.Snowboard.testPlugin();
                    expect(() => {
                        instance.snowboard.newMethod = () => {
                            return true;
                        };
                    }).toThrow(TypeError);
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
                'modules/system/tests/js/fixtures/framework/TestPlugin.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;

                    try {
                        // Check plugin caller
                        expect('testPlugin' in Snowboard).toEqual(true);
                        expect('testSingleton' in Snowboard).toEqual(false);

                        expect(Snowboard.hasPlugin('testPlugin')).toBe(true);
                        expect(Snowboard.getPluginNames()).toEqual(
                            expect.arrayContaining(['jsonparser', 'sanitizer', 'testplugin'])
                        );

                        const instance = Snowboard.testPlugin();

                        // Check plugin injected methods
                        expect(instance.snowboard).toBeDefined();
                        expect(instance.snowboard.getPlugin).toEqual(expect.any(Function));
                        expect(() => {
                            const method = instance.snowboard.initialise;
                        }).toThrow('cannot use');
                        expect(instance.destructor).toEqual(expect.any(Function));

                        // Check plugin method
                        expect(instance.testMethod).toBeDefined();
                        expect(instance.testMethod).toEqual(expect.any(Function));
                        expect(instance.testMethod()).toEqual('Tested');

                        // Check multiple instances
                        const instanceOne = Snowboard.testPlugin();
                        instanceOne.changed = true;
                        const instanceTwo = Snowboard.testPlugin();
                        expect(instanceOne).not.toEqual(instanceTwo);
                        const factory = Snowboard.getPlugin('testPlugin');
                        expect(factory.getInstances()).toEqual([instance, instanceOne, instanceTwo]);

                        // Remove plugin
                        Snowboard.removePlugin('testPlugin');
                        expect(Snowboard.hasPlugin('testPlugin')).toEqual(false);
                        expect(dom.window.Snowboard.getPluginNames()).toEqual(
                            expect.arrayContaining(['jsonparser', 'sanitizer'])
                        );
                        expect(Snowboard.testPlugin).not.toBeDefined();

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
                'modules/system/tests/js/fixtures/framework/TestSingleton.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;

                    try {
                        expect('testPlugin' in Snowboard).toEqual(false);
                        expect('testSingleton' in Snowboard).toEqual(true);

                         // Check plugin caller
                        expect(Snowboard.hasPlugin('testSingleton')).toBe(true);
                        expect(Snowboard.getPluginNames()).toEqual(
                            expect.arrayContaining(['jsonparser', 'sanitizer', 'testsingleton'])
                        );
                        expect(Snowboard.testSingleton).toEqual(expect.any(Function));

                        const instance = Snowboard.testSingleton();

                        // Check plugin injected methods
                        expect(instance.snowboard).toBeDefined();
                        expect(instance.snowboard.getPlugin).toEqual(expect.any(Function));
                        expect(() => {
                            const method = instance.snowboard.initialise;
                        }).toThrow('cannot use');
                        expect(instance.destructor).toEqual(expect.any(Function));

                        // Check plugin method
                        expect(instance.testMethod).toBeDefined();
                        expect(instance.testMethod).toEqual(expect.any(Function));
                        expect(instance.testMethod()).toEqual('Tested');

                        // Check multiple instances  (these should all be the same as this instance is a singleton)
                        const instanceOne = Snowboard.testSingleton();
                        instanceOne.changed = true;
                        const instanceTwo = Snowboard.testSingleton();
                        expect(instanceOne).toEqual(instanceTwo);
                        const factory = Snowboard.getPlugin('testSingleton');
                        expect(factory.getInstances()).toEqual([instance]);

                        // Remove plugin
                        Snowboard.removePlugin('testSingleton');
                        expect(Snowboard.hasPlugin('testSingleton')).toEqual(false);
                        expect(dom.window.Snowboard.getPluginNames()).toEqual(
                            expect.arrayContaining([ 'jsonparser', 'sanitizer'])
                        );
                        expect(Snowboard.testSingleton).not.toBeDefined();

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
                'modules/system/tests/js/fixtures/framework/TestListener.js',
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

    it('can listen and call global events that are simple closures', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/tests/js/fixtures/framework/TestClosureListener.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;

                    try {
                        expect(Snowboard.listensToEvent('eventOne')).toEqual(['testclosure']);
                        expect(Snowboard.listensToEvent('eventTwo')).toEqual(['testclosure']);
                        expect(Snowboard.listensToEvent('eventThree')).toEqual([]);

                        // Call global event one
                        const testClass = Snowboard.testClosure();
                        Snowboard.globalEvent('eventOne');
                        expect(testClass.eventResult).toEqual('Closure eventOne called');

                        // Call global event two - should fail as the test plugin doesn't have that method
                        Snowboard.globalEvent('eventTwo', 42);
                        expect(testClass.eventResult).toEqual('Closure eventTwo called with arg \'42\'');

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
                'modules/system/tests/js/fixtures/framework/TestPromiseListener.js',
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

    it('can listen and call global promise events that are simple closures', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/tests/js/fixtures/framework/TestPromiseClosureListener.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;

                    try {
                        expect(Snowboard.listensToEvent('promiseOne')).toEqual(['testclosure']);
                        expect(Snowboard.listensToEvent('promiseTwo')).toEqual(['testclosure']);
                        expect(Snowboard.listensToEvent('promiseThree')).toEqual([]);

                        // Call global event one
                        const testClass = Snowboard.testClosure();
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
                'modules/system/tests/js/fixtures/framework/TestHasDependencies.js',
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
                'modules/system/tests/js/fixtures/framework/TestHasDependencies.js',
                'modules/system/tests/js/fixtures/framework/TestDependencyOne.js',
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
                'modules/system/tests/js/fixtures/framework/TestDependencyTwo.js',
                'modules/system/tests/js/fixtures/framework/TestHasDependencies.js',
                'modules/system/tests/js/fixtures/framework/TestDependencyOne.js',
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
                'modules/system/tests/js/fixtures/framework/TestSingletonWithDependency.js',
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

    it('will allow plugins to call other plugin methods', function () {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/tests/js/fixtures/framework/TestDependencyOne.js',
                'modules/system/tests/js/fixtures/framework/TestSingletonWithDependency.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;
                    const instance = Snowboard.testSingleton();

                    expect(instance.dependencyTest()).toEqual('Tested');
                },
                (error) => {
                    throw error;
                }
            );
    });

    it('doesn\'t allow PluginBase or Singleton abstracts to be modified', function () {
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
                    expect(() => {
                        dom.window.Snowboard.PluginBase.newMethod = () => {
                            return true;
                        };
                    }).toThrow(TypeError);

                    expect(() => {
                        dom.window.Snowboard.PluginBase.destruct = () => {
                            return true;
                        };
                    }).toThrow(TypeError);

                    expect(() => {
                        dom.window.Snowboard.PluginBase.prototype.newMethod = () => {
                            return true;
                        };
                    }).toThrow(TypeError);

                    expect(() => {
                        dom.window.Snowboard.Singleton.newMethod = () => {
                            return true;
                        };
                    }).toThrow(TypeError);

                    expect(() => {
                        dom.window.Snowboard.Singleton.destruct = () => {
                            return true;
                        };
                    }).toThrow(TypeError);

                    expect(() => {
                        dom.window.Snowboard.Singleton.prototype.newMethod = () => {
                            return true;
                        };
                    }).toThrow(TypeError);
                },
            );
    });
});
