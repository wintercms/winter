<template>
        <div>
            <div class="w-full">
                <div class="product-search mx-6">
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
            <div class="w-full flex justify-center my-2">
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
                    class="btn btn-success wn-icon-file-arrow-up !ml-3"
                >
                    {{uploadString}}
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-8">
            <Product v-for="plugin in activePlugins" :product="plugin" type="plugin"></Product>
        </div>
</template>
<script>
import Product from "./Product.vue";
import installPopup from "../utils/install-popup";

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
    watch: {
        filter: function (value) {
            if (!value) {
                this.active = 'popular';
            }
        }
    },
    mounted() {
        if (this.plugins.length) {
            return;
        }
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
        installPopup,
    }
};
</script>
