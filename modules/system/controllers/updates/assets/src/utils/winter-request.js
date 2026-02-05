export const request = (handler, options) => {
    Snowboard.request(handler, options);
};

export const winterRequestPlugin = {
    install(app) {
        app.request = request;
        app.config.globalProperties.$request = request;
    },
};
