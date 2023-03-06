/* globals window */

class TestTrait {
    constructor() {
        this.traitProperty = 'Trait property';
    }

    testMethod() {
        return 'Trait method called';
    }

    testMethodTwo() {
        return 'Trait method two called';
    }

    testInferredProperty() {
        // The following property hasn't been defined in the trait, but will be defined in the classes
        // that use it.
        return this.inferredProperty;
    }

    testSnowboard() {
        // Snowboard isn't defined in the trait, but will be included in the classes that use it.
        return this.snowboard.hasPlugin('testClassWithTrait');
    }

    get property() {
        return this.traitProperty;
    }
}
