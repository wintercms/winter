import FakeDom from '../../../helpers/FakeDom';

const mockAjaxResponse = (dom, response) => {
    dom.window.winter.getModule('request').mock('doAjax', (instance) => {
        const resolved = Promise.resolve(response);

        // Mock events
        instance.winter.globalEvent('ajaxStart', instance, resolved);

        if (instance.element) {
            const event = new Event('ajaxPromise');
            event.promise = resolved;
            instance.element.dispatchEvent(event);
        }

        return resolved;
    });
};

describe('Request AJAX library', function () {
    it('can do a request', function (done) {
        FakeDom
            .new()
            .addScript([
                'modules/system/assets/js/framework-next/build/framework.js',
                'modules/system/assets/js/framework-next/build/framework-js-request.js'
            ])
            .render()
            .then(
                (dom) => {
                    mockAjaxResponse(dom, {
                        success: true
                    });

                    dom.window.winter.request(undefined, 'onTest', {
                        complete: (data, instance) => {
                            expect(data).toEqual({
                                success: true,
                            });
                            expect(instance.responseData).toEqual({
                                success: true,
                            });

                            done();
                            return false;
                        }
                    });
                }
            );
    });
});
