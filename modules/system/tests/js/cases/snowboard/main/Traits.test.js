import FakeDom from '../../../helpers/FakeDom';

describe('Snowboard plugins with traits', function () {
    it('load traits correctly and receive all methods and properties', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/tests/js/fixtures/framework/TestTrait.js',
                'modules/system/tests/js/fixtures/framework/TestClassWithTrait.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;

                    try {
                        const fixture = Snowboard.testClassWithTrait();

                        expect(fixture.testMethod()).toEqual('Trait method called');
                        expect(fixture.testMethodTwo()).toEqual('Trait method two called');
                        expect(fixture.traitProperty).toEqual('Trait property');
                        expect(fixture.property).toEqual('Trait property');
                        expect(fixture.internalMethod()).toEqual('Internal method called');
                        expect(fixture.internalProperty).toEqual('Internal property');

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

    it('allows methods in traits to be overwritten by target instance', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/tests/js/fixtures/framework/TestTrait.js',
                'modules/system/tests/js/fixtures/framework/TestClassWithTraitOverriddenMethod.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;

                    try {
                        const fixture = Snowboard.testClassWithTraitOverriddenMethod();

                        expect(fixture.testMethod()).toEqual('Overridden method called');
                        expect(fixture.testMethodTwo()).toEqual('Trait method two called');
                        expect(fixture.traitProperty).toEqual('Overridden property');
                        expect(fixture.property).toEqual('Overridden property');
                        expect(fixture.internalMethod()).toEqual('Internal method called');
                        expect(fixture.internalProperty).toEqual('Internal property');

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

    it('can load multiple traits and combine all methods and properties', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/tests/js/fixtures/framework/TestTrait.js',
                'modules/system/tests/js/fixtures/framework/TestTraitTwo.js',
                'modules/system/tests/js/fixtures/framework/TestClassWithMultipleTraits.js',
            ])
            .render()
            .then(
                (dom) => {
                    // Run assertions
                    const Snowboard = dom.window.Snowboard;

                    try {
                        const fixture = Snowboard.testClassWithMultipleTraits();

                        expect(fixture.testMethod()).toEqual('Trait method called');
                        // The following method is defined in both traits, but since TestTrait is loaded
                        // first, it becomes the precedented method.
                        expect(fixture.testMethodTwo()).toEqual('Trait method two called');
                        expect(fixture.testMethodThree()).toEqual('Trait two method three called');
                        expect(fixture.testMethodFour()).toEqual('Trait two method four called');
                        // As above, while both traits define this property, TestTrait loaded first so
                        // it becomes the precedented property.
                        expect(fixture.traitProperty).toEqual('Trait property');
                        expect(fixture.traitTwoProperty).toEqual('Trait two property');
                        expect(fixture.property).toEqual('Trait property');
                        expect(fixture.internalMethod()).toEqual('Internal method called');
                        expect(fixture.internalProperty).toEqual('Internal property');

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
});
