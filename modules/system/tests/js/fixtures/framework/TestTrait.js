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

    get property() {
        return this.traitProperty;
    }
}
