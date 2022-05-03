export default class AssetLoader extends window.Snowboard.Singleton {
    listens() {
        return {
            ajaxLoadAssets: 'processAssets',
        };
    }

    processAssets(assets) {
        console.log(assets);
    }
}
