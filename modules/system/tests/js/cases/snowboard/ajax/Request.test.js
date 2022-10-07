import FakeDom from '../../../helpers/FakeDom';
import FetchMock from '../../../helpers/FetchMock';

jest.setTimeout(2000);

describe('Request AJAX library', function () {
    it('can be setup via a global event', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render()
            .then(
                (dom) => {
                    dom.window.Snowboard.getPlugin('request').mock('doAjax', (instance) => {
                        // Simulate success response
                        const resolved = Promise.resolve({
                            success: true
                        });

                        // Mock events
                        instance.snowboard.globalEvent('ajaxStart', instance, resolved);

                        if (instance.element) {
                            const event = new dom.window.Event('ajaxPromise');
                            event.promise = resolved;
                            instance.element.dispatchEvent(event);
                        }

                        return resolved;
                    });

                    // Listen to global event
                    dom.window.Snowboard.addPlugin('testListener', class TestListener extends dom.window.Snowboard.Singleton {
                        listens() {
                            return {
                                ajaxSetup: 'ajaxSetup',
                            };
                        }

                        ajaxSetup(instance) {
                            instance.handler = 'onChanged';
                        }
                    });

                    dom.window.Snowboard.request(undefined, 'onTest', {
                        complete: (data, instance) => {
                            expect(instance.handler).toEqual('onChanged');
                            done();
                        }
                    });
                }
            );
    });

    it('can be cancelled on setup via a global event', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render()
            .then(
                (dom) => {
                    dom.window.Snowboard.getPlugin('request').mock('doAjax', (instance) => {
                        // Simulate success response
                        const resolved = Promise.resolve({
                            success: true
                        });

                        // Mock events
                        instance.snowboard.globalEvent('ajaxStart', instance, resolved);

                        if (instance.element) {
                            const event = new dom.window.Event('ajaxPromise');
                            event.promise = resolved;
                            instance.element.dispatchEvent(event);
                        }

                        return resolved;
                    });

                    // Listen to global event
                    dom.window.Snowboard.addPlugin('testListener', class TestListener extends dom.window.Snowboard.Singleton {
                        listens() {
                            return {
                                ajaxSetup: 'ajaxSetup',
                            };
                        }

                        ajaxSetup() {
                            // Should cancel
                            return false;
                        }
                    });

                    const instance = dom.window.Snowboard.request(undefined, 'onTest', {
                        complete: (data, instance) => {
                            done(new Error('Request did not cancel'));
                        }
                    });
                    expect(instance.cancelled).toEqual(true);
                    done();
                }
            );
    });

    it('can be setup via a listener on the HTML element', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render('<button id="testElement">Test</button>')
            .then(
                (dom) => {
                    dom.window.Snowboard.getPlugin('request').mock('doAjax', (instance) => {
                        // Simulate success response
                        const resolved = Promise.resolve({
                            success: true
                        });

                        // Mock events
                        instance.snowboard.globalEvent('ajaxStart', instance, resolved);

                        if (instance.element) {
                            const event = new dom.window.Event('ajaxPromise');
                            event.promise = resolved;
                            instance.element.dispatchEvent(event);
                        }

                        return resolved;
                    });

                    // Listen to HTML element event
                    const element = dom.window.document.getElementById('testElement');
                    element.addEventListener('ajaxSetup', (event) => {
                        expect(event.request).toBeDefined();
                        event.request.handler = 'onChanged';
                    });


                    dom.window.Snowboard.request(element, 'onTest', {
                        complete: (data, instance) => {
                            expect(instance.handler).toEqual('onChanged');
                            done();
                        }
                    });
                }
            );
    });

    it('can be cancelled on setup via a listener on the HTML element', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render('<button id="testElement">Test</button>')
            .then(
                (dom) => {
                    dom.window.Snowboard.getPlugin('request').mock('doAjax', (instance) => {
                        // Simulate success response
                        const resolved = Promise.resolve({
                            success: true
                        });

                        // Mock events
                        instance.snowboard.globalEvent('ajaxStart', instance, resolved);

                        if (instance.element) {
                            const event = new dom.window.Event('ajaxPromise');
                            event.promise = resolved;
                            instance.element.dispatchEvent(event);
                        }

                        return resolved;
                    });

                    // Listen to HTML element event
                    const element = dom.window.document.getElementById('testElement');
                    element.addEventListener('ajaxSetup', (event) => {
                        event.preventDefault();
                    });

                    const instance = dom.window.Snowboard.request(element, 'onTest', {
                        complete: (data, instance) => {
                            done(new Error('Request did not cancel'));
                        }
                    });
                    expect(instance.cancelled).toEqual(true);
                    done();
                }
            );
    });

    it('can do a request and listen for completion', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render()
            .then(
                (dom) => {
                    dom.window.Snowboard.getPlugin('request').mock('doAjax', (instance) => {
                        // Simulate success response
                        const resolved = Promise.resolve({
                            success: true
                        });

                        // Mock events
                        instance.snowboard.globalEvent('ajaxStart', instance, resolved);

                        if (instance.element) {
                            const event = new Event('ajaxPromise');
                            event.promise = resolved;
                            instance.element.dispatchEvent(event);
                        }

                        return resolved;
                    });

                    dom.window.Snowboard.request(undefined, 'onTest', {
                        complete: (data, instance) => {
                            expect(data).toEqual({
                                success: true,
                            });
                            expect(instance.responseData).toEqual({
                                success: true,
                            });
                            expect(instance.responseError).toEqual(null);

                            done();
                            return false;
                        }
                    });
                }
            );
    });

    it('can do a request and listen for completion via a global event', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render()
            .then(
                (dom) => {
                    dom.window.Snowboard.getPlugin('request').mock('doAjax', (instance) => {
                        // Simulate success response
                        const resolved = Promise.resolve({
                            success: true
                        });

                        // Mock events
                        instance.snowboard.globalEvent('ajaxStart', instance, resolved);

                        if (instance.element) {
                            const event = new Event('ajaxPromise');
                            event.promise = resolved;
                            instance.element.dispatchEvent(event);
                        }

                        return resolved;
                    });

                    // Listen to global event
                    dom.window.Snowboard.addPlugin('testListener', class TestListener extends dom.window.Snowboard.Singleton {
                        listens() {
                            return {
                                ajaxDone: 'ajaxDone',
                            };
                        }

                        ajaxDone(data, instance) {
                            expect(data).toEqual({
                                success: true,
                            });
                            expect(instance.responseData).toEqual({
                                success: true,
                            });
                            expect(instance.responseError).toEqual(null);

                            done();
                        }
                    });

                    dom.window.Snowboard.request(undefined, 'onTest');
                }
            );
    });

    it('can do a request and listen for completion via a listener on the HTML element', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render('<button id="testElement">Test</button>')
            .then(
                (dom) => {
                    dom.window.Snowboard.getPlugin('request').mock('doAjax', (instance) => {
                        // Simulate success response
                        const resolved = Promise.resolve({
                            success: true
                        });

                        // Mock events
                        instance.snowboard.globalEvent('ajaxStart', instance, resolved);

                        if (instance.element) {
                            const event = new dom.window.Event('ajaxPromise');
                            event.promise = resolved;
                            instance.element.dispatchEvent(event);
                        }

                        return resolved;
                    });

                    // Listen to HTML element event
                    const element = dom.window.document.getElementById('testElement');
                    element.addEventListener('ajaxAlways', (event) => {
                        expect(event.request).toBeDefined();
                        expect(event.responseData).toEqual({
                            success: true
                        });
                        expect(event.responseError).toEqual(null);
                        done();
                    });

                    dom.window.Snowboard.request(element, 'onTest');
                }
            );
    });

    it('can do a request and listen for success', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render()
            .then(
                (dom) => {
                    dom.window.Snowboard.getPlugin('request').mock('doAjax', (instance) => {
                        // Simulate success response
                        const resolved = Promise.resolve({
                            success: true
                        });

                        // Mock events
                        instance.snowboard.globalEvent('ajaxStart', instance, resolved);

                        if (instance.element) {
                            const event = new Event('ajaxPromise');
                            event.promise = resolved;
                            instance.element.dispatchEvent(event);
                        }

                        return resolved;
                    });

                    dom.window.Snowboard.request(undefined, 'onTest', {
                        success: (data, instance) => {
                            expect(data).toEqual({
                                success: true,
                            });
                            expect(instance.responseData).toEqual({
                                success: true,
                            });
                            expect(instance.responseError).toEqual(null);
                        },
                        complete: (data, instance) => {
                            expect(data).toEqual({
                                success: true,
                            });
                            expect(instance.responseData).toEqual({
                                success: true,
                            });
                            expect(instance.responseError).toEqual(null);

                            done();
                        },
                    });
                }
            );
    });

    it('can do a request and listen for success via a global event', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render()
            .then(
                (dom) => {
                    dom.window.Snowboard.getPlugin('request').mock('doAjax', (instance) => {
                        // Simulate success response
                        const resolved = Promise.resolve({
                            success: true
                        });

                        // Mock events
                        instance.snowboard.globalEvent('ajaxStart', instance, resolved);

                        if (instance.element) {
                            const event = new Event('ajaxPromise');
                            event.promise = resolved;
                            instance.element.dispatchEvent(event);
                        }

                        return resolved;
                    });

                    // Listen to global event
                    dom.window.Snowboard.addPlugin('testListener', class TestListener extends dom.window.Snowboard.Singleton {
                        listens() {
                            return {
                                ajaxSuccess: 'ajaxSuccess',
                            };
                        }

                        ajaxSuccess(data, instance) {
                            expect(data).toEqual({
                                success: true,
                            })
                            expect(instance.responseData).toEqual({
                                success: true,
                            });
                            expect(instance.responseError).toEqual(null);

                            done();
                        }
                    });

                    dom.window.Snowboard.request(undefined, 'onTest');
                }
            );
    });

    it('can do a request and listen for failure', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render()
            .then(
                (dom) => {
                    dom.window.Snowboard.getPlugin('request').mock('doAjax', (instance) => {
                        // Simulate error response
                        const resolved = Promise.reject('This is an error');

                        // Mock events
                        instance.snowboard.globalEvent('ajaxStart', instance, resolved);

                        if (instance.element) {
                            const event = new Event('ajaxPromise');
                            event.promise = resolved;
                            instance.element.dispatchEvent(event);
                        }

                        return resolved;
                    });

                    dom.window.Snowboard.request(undefined, 'onTest', {
                        error: (data, instance) => {
                            expect(data).toEqual('This is an error');
                            expect(instance.responseData).toEqual(null);
                            expect(instance.responseError).toEqual('This is an error');
                        },
                        complete: (data, instance) => {
                            // Data will be null because no data was provided in the response.
                            expect(data).toBeNull();
                            expect(instance.responseData).toEqual(null);
                            expect(instance.responseError).toEqual('This is an error');

                            done();
                        },
                    });
                }
            );
    });

    it('can do a request and listen for failure via global event', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render()
            .then(
                (dom) => {
                    dom.window.Snowboard.getPlugin('request').mock('doAjax', (instance) => {
                        // Simulate error response
                        const resolved = Promise.reject('This is an error');

                        // Mock events
                        instance.snowboard.globalEvent('ajaxStart', instance, resolved);

                        if (instance.element) {
                            const event = new Event('ajaxPromise');
                            event.promise = resolved;
                            instance.element.dispatchEvent(event);
                        }

                        return resolved;
                    });

                    // Listen to global event
                    dom.window.Snowboard.addPlugin('testListener', class TestListener extends dom.window.Snowboard.Singleton {
                        listens() {
                            return {
                                ajaxError: 'ajaxError',
                            };
                        }

                        ajaxError(data, instance) {
                            expect(data).toEqual('This is an error')
                            expect(instance.responseData).toEqual(null);
                            expect(instance.responseError).toEqual('This is an error');

                            done();
                        }
                    });

                    dom.window.Snowboard.request(undefined, 'onTest');
                }
            );
    });

    it('requires a valid HTML element if provided', function () {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render()
            .then(
                (dom) => {
                    expect(() => {
                        const docFragment = dom.window.document.createDocumentFragment();
                        dom.window.Snowboard.request(docFragment, 'onTest');
                    }).toThrow('The element provided must be an Element instance');
                }
            );
    });

    it('requires a handler to be specified', function () {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render()
            .then(
                (dom) => {
                    expect(() => {
                        dom.window.Snowboard.request(undefined, undefined);
                    }).toThrow('The AJAX handler name is not specified');
                }
            );
    });

    it('requires a handler to be of the correct format', function () {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render()
            .then(
                (dom) => {
                    expect(() => {
                        dom.window.Snowboard.request(undefined, 'notRight');
                    }).toThrow('Invalid AJAX handler name');
                }
            );
    });

    it('can be run detached from an element with two parameters (handler and options)', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render()
            .then(
                (dom) => {
                    dom.window.Snowboard.getPlugin('request').mock('doAjax', (instance) => {
                        // Simulate success response
                        const resolved = Promise.resolve({
                            success: true
                        });

                        // Mock events
                        instance.snowboard.globalEvent('ajaxStart', instance, resolved);

                        if (instance.element) {
                            const event = new Event('ajaxPromise');
                            event.promise = resolved;
                            instance.element.dispatchEvent(event);
                        }

                        return resolved;
                    });

                    dom.window.Snowboard.request('onTest', {
                        complete: (data, instance) => {
                            done();
                            return false;
                        }
                    });
                }
            );
    });

    it('can correctly receive a mocked 404 JSON response', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/build/manifest.js',
                'modules/system/assets/js/snowboard/build/snowboard.vendor.js',
                'modules/system/assets/js/snowboard/build/snowboard.base.js',
                'modules/system/assets/js/snowboard/build/snowboard.request.js'
            ])
            .render()
            .then(
                (dom) => {
                    dom.window.fetch = FetchMock(
                        dom,
                        404,
                        '{"title":"404 Popup","markup":"<div>\\n    <div class=\\"w-full bg-black\\">\\n    <h2 class=\\"p-6 text-white text-2xl\\">Content not found<\\/h2>\\n<\\/div>    <div class=\\"container p-6 mx-auto\\">\\n    The requested popup could not be found, please try again.\\n<\\/div><\\/div>\\n"}',
                        {
                            'Content-Type': 'application/json'
                        }
                    );

                    dom.window.Snowboard.request('onTest', {
                        error: (error, instance) => {
                            expect(error).toEqual({
                                title: '404 Popup',
                                markup: '<div>\n    <div class="w-full bg-black">\n    <h2 class="p-6 text-white text-2xl">Content not found</h2>\n</div>    <div class="container p-6 mx-auto">\n    The requested popup could not be found, please try again.\n</div></div>\n'
                            })
                            expect(instance).toBeDefined();
                            expect(instance.responseData).toEqual(null);
                            expect(instance.responseError).toEqual({
                                title: '404 Popup',
                                markup: '<div>\n    <div class="w-full bg-black">\n    <h2 class="p-6 text-white text-2xl">Content not found</h2>\n</div>    <div class="container p-6 mx-auto">\n    The requested popup could not be found, please try again.\n</div></div>\n'
                            });
                            done();
                        }
                    });
                }
            );
    });
});
