<template>
    <div class="container-fluid">
        <div class="row mx-5">
            <div class="col-12">
                <div class="product-search">
                    <input
                        ref="search"
                        name="code"
                        id="pluginSearchInput"
                        class="product-search-input search-input-lg typeahead"
                        :placeholder="searchString"
                        data-search-type="plugins"
                        v-model="filter"
                        @keydown="activePlugins = 'all'"
                    />
                    <i class="icon icon-search"></i>
                    <i class="icon loading" style="display: none"></i>
                </div>
            </div>
            <div class="col-12 btn-row">
                <div class="btn-group" role="group" aria-label="...">
                    <button type="button"
                            :class="`btn btn-${active === 'popular' ? 'primary' : 'default'}`"
                            @click="activePlugins = 'popular'; filter = null;"
                    >Popular</button>
                    <button type="button"
                            :class="`btn btn-${active === 'featured' ? 'primary' : 'default'}`"
                            @click="activePlugins = 'featured'; filter = null;"
                    >Featured</button>
                </div>

                <button
                    type="button"
                    data-control="popup"
                    data-handler="onLoadPluginUploader"
                    tabindex="-1"
                    class="btn btn-success wn-icon-file-arrow-up"
                >
                    {{uploadString}}
                </button>
            </div>
        </div>
        <div class="products row m-t-sm">
            <Product v-for="plugin in activePlugins" :product="plugin" type="plugin"></Product>
        </div>
    </div>
</template>
<script>
import Product from "./Product.vue";

export default {
    components: {Product},
    props: ['searchString', 'uploadString'],
    data: () => ({
        active: 'popular',
        plugins: {},
        filter: null
    }),
    computed: {
        activePlugins: {
            get() {
                if (this.filter) {
                    return this.plugins.all.filter((plugin) => {
                        return plugin.name.includes(this.filter)
                            || plugin.description.includes(this.filter)
                            || plugin.package.includes(this.filter);
                    });
                }
                return this.plugins[this.active];
            },
            set(value) {
                this.active = value;
            }
        }
    },
    mounted() {
        this.$request('onGetMarketplacePlugins', {
            success: (response) => {
                this.plugins = response.result;

                if (localStorage.winterInstalling) {
                    const installing = JSON.parse(localStorage.winterInstalling);
                    if (installing.length > 0) {
                        this.displayInstall(installing[installing.length - 1]);
                    }
                }
            }
        });
    },
    methods: {
        displayInstall(installKey) {
            $.popup({
                size: 'large installer-popup',
                content: `
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Installing...</h4>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Okay</button>
                    </div>
                `
            });

            const popup = document.querySelector('.installer-popup .modal-body');

            const prepareMessage = (str) => {
                return `<div class="install-message">${
                    str.split("\n").filter((line) => line.indexOf('FINISHED:') === 0 ? false : !!line).map((line) => {
                        ['INFO', 'ERROR'].forEach((status) => {
                            if (line.indexOf(status) === 0) {
                                line = `<span class="message-${status.toLowerCase()}">${status}</span> ${line.substring(status.length + 1)}`;
                            }
                        });

                        let search;

                        if (search = line.match(/^[\d].*:\ /)) {
                            line = `<span class="message-version">${search[0].replace(':', '').trim()}</span>:${line.substring(search[0].length + 1)}`;
                        }

                        if (search = line.match(/\.*?[\d][\w]*DONE$/)) {
                            line = `${line.substring(0, line.length - search[0].length)} <span class="message-done">${search[0].match(/\.*?[\d][\w]*DONE$/)[0].replace(/\.*/, '').replace('DONE', '')}</span>`;
                        }

                        return `<pre>${line}</pre>`;
                    }).join("\n")
                }</div>`;
            };

            const checkStatus = () => {
                this.$request('onInstallProductStatus', {
                    data: {
                        install_key: installKey
                    },
                    success: (statusResponse) => {
                        popup.innerHTML = prepareMessage(statusResponse.data);

                        if (!statusResponse.done) {
                            return setTimeout(checkStatus, 500);
                        }

                        const store = JSON.parse(localStorage.winterInstalling);
                        store.splice(store.indexOf(installKey), 1);
                        localStorage.winterInstalling = JSON.stringify(store);
                    }
                })
            };
            checkStatus();
        }
    }
};
</script>
<style>
.mt-lg-3 {
    margin-top: 15px;
}
@media (min-width: 992px) {
    .text-md-right {
        text-align: right;
    }
    .mt-lg-3 {
        margin-top: 0;
    }
}
.btn-row {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin: auto auto 15px auto;
}
.mx-5 {
    margin: auto 5rem;
}
.products {
    display: flex;
    flex-wrap: wrap;
}
</style>
