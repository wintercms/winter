/* globals it, describe */

import { assert } from 'chai'
import FakeDom from 'helpers/FakeDom'

describe('modules/system/assets/js/framework/base.js', function () {
    'use strict';

    describe('The Winter JS Framework', function () {
        // Set a reasonable timeout
        this.timeout(2000)

        it('initialises correctly', function (done) {
            FakeDom.new()
                .addScript('modules/system/assets/js/framework/base.js')
                .render()
                .then(
                    (dom) => {
                        // Run assertions
                        try {
                            assert.exists(dom.window.winter)
                            assert.equal('object', typeof dom.window.winter)
                            assert.exists(dom.window.winter.extend)

                            done()
                        } catch (error) {
                            done(error)
                        }
                    },
                    (error) => {
                        throw error
                    }
                )
        })

        it('can load an unextendable module and run a module method', function (done) {
            FakeDom.new()
                .addScript([
                    'modules/system/assets/js/framework/base.js',
                    'tests/js/fixtures/framework/TestModule.js',
                ])
                .render()
                .then(
                    (dom) => {
                        // Run assertions
                        const winter = dom.window.winter;

                        try {
                            assert.isTrue(winter.hasModule('TestModule'))
                            assert.isFunction(winter.TestModule)

                            // Check extension methods
                            assert.isFalse(winter.moduleIsExtendable('TestModule'))
                            assert.isFalse(winter.moduleIsAttachable('TestModule'))
                            assert.notExists(winter.TestModule().extend)
                            assert.notExists(winter.TestModule().callExtendable)
                            assert.notExists(winter.TestModule().callAttachable)

                            // Check module method
                            assert.exists(winter.TestModule().testMethod)
                            assert.isFunction(winter.TestModule().testMethod)
                            assert.equal('Tested', winter.TestModule().testMethod())

                            done()
                        } catch (error) {
                            done(error)
                        }
                    },
                    (error) => {
                        throw error
                    }
                )
        })

        it('can load an extendable module and run a module method', function (done) {
            FakeDom.new()
                .addScript([
                    'modules/system/assets/js/framework/base.js',
                    'tests/js/fixtures/framework/ExtendableTestModule.js',
                ])
                .render()
                .then(
                    (dom) => {
                        // Run assertions
                        const winter = dom.window.winter;

                        try {
                            assert.isTrue(winter.hasModule('ExtendableTestModule'))
                            assert.isFunction(winter.ExtendableTestModule)

                            // Check extension methods
                            assert.isTrue(winter.moduleIsExtendable('ExtendableTestModule'))
                            assert.isFalse(winter.moduleIsAttachable('ExtendableTestModule'))
                            assert.exists(winter.ExtendableTestModule().extend)
                            assert.exists(winter.ExtendableTestModule().callExtendable)
                            assert.notExists(winter.ExtendableTestModule().callAttachable)

                            // Check module method
                            assert.exists(winter.ExtendableTestModule().extendableMethod)
                            assert.isFunction(winter.ExtendableTestModule().extendableMethod)
                            assert.equal('default', winter.ExtendableTestModule().extendableMethod())

                            done()
                        } catch (error) {
                            done(error)
                        }
                    },
                    (error) => {
                        throw error
                    }
                )
        })

        it('can load an extendable module with an extension and run the same module method with a different result', function (done) {
            FakeDom.new()
                .addScript([
                    'modules/system/assets/js/framework/base.js',
                    'tests/js/fixtures/framework/ExtendableTestModule.js',
                    'tests/js/fixtures/framework/ExtendableTestModuleExtension.js',
                ])
                .render()
                .then(
                    (dom) => {
                        // Run assertions
                        const winter = dom.window.winter;

                        try {
                            // Check module method
                            assert.exists(winter.ExtendableTestModule().extendableMethod)
                            assert.isFunction(winter.ExtendableTestModule().extendableMethod)
                            assert.equal('extended', winter.ExtendableTestModule().extendableMethod())

                            done()
                        } catch (error) {
                            done(error)
                        }
                    },
                    (error) => {
                        throw error
                    }
                )
        })

        it('will only accept the first extension for the method result', function (done) {
            FakeDom.new()
                .addScript([
                    'modules/system/assets/js/framework/base.js',
                    'tests/js/fixtures/framework/ExtendableTestModule.js',
                    'tests/js/fixtures/framework/ExtendableTestModuleExtension.js',
                    'tests/js/fixtures/framework/ExtendableTestModuleSecondExtension.js',
                ])
                .render()
                .then(
                    (dom) => {
                        // Run assertions
                        const winter = dom.window.winter;

                        try {
                            // Check module method
                            assert.equal('extended', winter.ExtendableTestModule().extendableMethod())

                            done()
                        } catch (error) {
                            done(error)
                        }
                    },
                    (error) => {
                        throw error
                    }
                )
        })
    })
})
