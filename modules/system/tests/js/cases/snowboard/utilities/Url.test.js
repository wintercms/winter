import Url from 'snowboard/utilities/Url';

describe('The Url utility', function () {
    let url;

    beforeEach(function () {
        url = new Url({});
        url.foundBaseUrl = 'https://example.com/';
        url.foundAssetUrl = 'https://example.com/assets/';
    });

    it('resolves relative paths against the base URL', function () {
        expect(url.to('')).toBe('https://example.com/');
        expect(url.to('about')).toBe('https://example.com/about');
        expect(url.to('/about')).toBe('https://example.com/about');
    });

    it('resolves relative paths against the asset URL', function () {
        expect(url.asset('js/app.js')).toBe('https://example.com/assets/js/app.js');
        expect(url.asset('/js/app.js')).toBe('https://example.com/assets/js/app.js');
    });

    it('returns absolute URLs unchanged', function () {
        expect(url.to('https://cdn.example.org/page')).toBe('https://cdn.example.org/page');
        expect(url.asset('https://cdn.example.org/app.js')).toBe('https://cdn.example.org/app.js');
    });

    it('returns protocol-relative URLs unchanged', function () {
        expect(url.to('//cdn.example.org/page')).toBe('//cdn.example.org/page');
        expect(url.asset('//maps.googleapis.com/maps/api/js?libraries=places&key=abc'))
            .toBe('//maps.googleapis.com/maps/api/js?libraries=places&key=abc');
    });
});
