export default class AssetLoader extends window.Snowboard.Singleton {
    listens() {
        return {
            ajaxLoadAssets: 'processAssets',
        };
    }

    processAssets(assets) {
        return new Promise((resolve, reject) => {
            const promises = [];

            if (assets.js && assets.js.length > 0) {
                assets.js.forEach((script) => {
                    promises.push(this.loadScript(script));
                });
            }

            if (assets.css && assets.css.length > 0) {
                assets.css.forEach((style) => {
                    promises.push(this.loadStyle(style));
                });
            }

            if (assets.img && assets.img.length > 0) {
                assets.img.forEach((image) => {
                    promises.push(this.loadImage(image));
                });
            }

            if (promises.length === 0) {
                resolve();
            }

            Promise.all(promises).then(
                () => {
                    resolve();
                },
                (error) => {
                    reject(error);
                },
            );
        });
    }

    loadScript(script) {
        return new Promise((resolve, reject) => {
            // Check that script is not already loaded
            const loaded = document.querySelector(`script[src="${script}"]`);
            if (loaded) {
                resolve();
            }

            // Create script
            const domScript = document.createElement('script');
            domScript.setAttribute('type', 'text/javascript');
            domScript.setAttribute('src', script);
            domScript.addEventListener('load', () => {
                this.snowboard.globalEvent('backend.assetLoaded', 'script', script, domScript);
                resolve();
            });
            domScript.addEventListener('error', () => {
                this.snowboard.globalEvent('backend.assetError', 'script', script, domScript);
                reject(new Error(`Unable to load script file: "${script}"`));
            });
            document.body.append(domScript);
        });
    }

    loadStyle(style) {
        return new Promise((resolve, reject) => {
            // Check that stylesheet is not already loaded
            const loaded = document.querySelector(`link[rel="stylesheet"][href="${style}"]`);
            if (loaded) {
                resolve();
            }

            // Create stylesheet
            const domCss = document.createElement('link');
            domCss.setAttribute('rel', 'stylesheet');
            domCss.setAttribute('href', style);
            domCss.addEventListener('load', () => {
                this.snowboard.globalEvent('backend.assetLoaded', 'style', style, domCss);
                resolve();
            });
            domCss.addEventListener('error', () => {
                this.snowboard.globalEvent('backend.assetError', 'style', style, domCss);
                reject(new Error(`Unable to load stylesheet file: "${style}"`));
            });
            document.head.append(domCss);
        });
    }

    loadImage(image) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.addEventListener('load', () => {
                this.snowboard.globalEvent('backend.assetLoaded', 'image', image, img);
                resolve();
            });
            img.addEventListener('error', () => {
                this.snowboard.globalEvent('backend.assetError', 'image', image, img);
                reject(new Error(`Unable to load image file: "${image}"`));
            });
            img.src = image;
        });
    }
}
