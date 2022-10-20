export default function (dom, statusCode, body, headers, jsonPromise, textPromise) {
    return function () {
        return Promise.resolve({
            ok: statusCode >= 200 && statusCode < 300,
            status: statusCode,
            headers: new dom.window.Headers(headers),
            json: () => {
                if (jsonPromise) {
                    return jsonPromise;
                }

                return Promise.resolve(JSON.parse(body));
            },
            text: () => {
                if (textPromise) {
                    return textPromise;
                }

                return Promise.resolve(body);
            },
        });
    }
};
