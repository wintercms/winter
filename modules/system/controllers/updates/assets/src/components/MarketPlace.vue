<template>
    <div ref="root" class="-mx-3 font-sans">
        <div>
            <div class="flex flex-wrap justify-center gap-4 px-4">
                <label class="!flex relative w-full-storm-fix lg:w-auto items-center border border-blue-200 pl-4 pr-12 py-2 rounded-3xl text-2xl bg-white cursor-text">
                    <i class="icon icon-search text-2xl bg-blue-200 p-4 rounded-full mr-2"></i>
                    <input
                        id="pluginSearchInput"
                        class="border-transparent focus:border-transparent focus:ring-0 !text-3xl"
                        autocomplete="off"
                        :placeholder="searchString"
                        v-model="search"
                    >
                    <i v-on:click="search = ''"
                       :class="`${search ? 'opacity-100' : 'rotate-90 opacity-0'} icon icon-times text-4xl mr-6 absolute right-0 cursor-pointer transition-all duration-400`"
                    ></i>
                </label>
                <ul class="flex flex-row bg-white rounded-3xl border border-blue-200 h-auto p-2 !mb-2 items-center">
                    <li v-for="type in ['all', 'free', 'paid']">
                        <label
                            class="flex bg-white p-4 rounded-3xl w-full items-center cursor-pointer select-none transition-all duration-300 group has-[input:checked]:bg-blue-200 !mb-0"
                        >
                            <input type="radio"
                                   name="filter"
                                   :value="type"
                                   v-model="filterPrice"
                                   class="hidden [&:checked+span]:text-gray-900"
                            >
                            <span class="mx-4 text-2xl text-gray-600 capitalize font-bold">{{ type }}</span>
                        </label>
                    </li>
                </ul>
                <ul class="flex flex-row h-auto !mb-2 items-center">
                    <li v-for="feature in [{label: 'Translate Support', key: 'translate', icon: 'icon-language'}]" class="h-full">
                        <label
                            class="!flex bg-white border border-blue-200 rounded-3xl h-full w-full items-center cursor-pointer select-none transition-all duration-300 group has-[input:checked]:bg-blue-200 !mb-0"
                        >
                            <input type="checkbox"
                                   :name="`feature_${feature.key}`"
                                   :value="feature.key"
                                   v-model="filterFeatures"
                                   class="hidden"
                            >
                            <span class="inline-flex size-14 -mt-1 items-center">
                                <i :class="`${feature.icon} mx-auto text-2xl`"></i>
                            </span>
                            <span class="mr-4 text-2xl text-gray-600 capitalize font-bold">{{ feature.label }}</span>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
        <div v-if="loaded" class="flex flex-row gap-6 max-w-[1325px] mx-auto">
            <div class="hidden-storm-fix xl:flex flex-col min-w-[225px] p-1">
                <strong class="text-3xl pb-4">Categories</strong>
                <ul>
                    <li v-for="category in categories" class="mb-1">
                        <label class="flex bg-white border border-blue-200 p-4 rounded-3xl shadow-md w-full items-center cursor-pointer select-none hover:ml-3 transition-all has-[input:checked]:bg-blue-200">
                            <input type="checkbox" v-model="filterCategories" :value="category.id" class="hidden [&:checked+span]:bg-blue-400 [&:checked+span]:text-white">
                            <span class="inline-flex size-14 bg-blue-200 rounded-full transition-all duration-300 items-center">
                                <i :class="`${category.icon} mx-auto text-2xl`"></i>
                            </span>
                            <span class="mx-4 text-2xl text-gray-900">{{ category.name }}</span>
                        </label>
                    </li>
                </ul>

                <div class="mt-4">
                    <strong class="text-3xl pb-4">Have something custom?</strong>
                    <p>Click here to manually upload a plugin.</p>
                    <button
                        type="button"
                        data-control="popup"
                        :data-handler="uploadHandler"
                        tabindex="-1"
                        class="btn btn-success wn-icon-file-arrow-up w-full"
                    >
                        {{uploadString}}
                    </button>
                </div>
            </div>
            <div class="mt-8 w-full">
                <div class="w-full flex flex-col lg:flex-row flex-wrap items-center gap-4 justify-between flex-grow flex-shrink basis-auto transition-all duration-300">
                    <div>
                        <span :class="`${results.length || 'invisible'} block text-center lg:text-left min-w-[250px]`">
                            Showing <strong>{{ showingFromProduct }}</strong>
                            to <strong>{{ (perPage * (page - 1)) + pageProducts.length }}</strong>
                            of <strong>{{ results.length }}</strong> results
                        </span>
                    </div>
                    <div class="flex gap-4 items-center order-first lg:order-0">
                        <span>Sort:</span>
                        <select v-model="sort" class="rounded-3xl p-3 w-[125px] border border-blue-200">
                            <option value="new" selected>Newest</option>
                            <option value="downloads">Downloaded</option>
                            <option value="stars">Stars</option>
                        </select>

                        <span>Per Page:</span>
                        <select v-model.number="perPage" class="rounded-3xl p-3 w-[125px] border border-blue-200">
                            <option selected value="9">Default</option>
                            <option value="20">20</option>
                            <option value="100">100</option>
                            <option :value="Number.MAX_SAFE_INTEGER">All</option>
                        </select>
                    </div>
                    <Pagination v-model:page="page" :items="results.length" :per-page="perPage" :element-count="9"></Pagination>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-8 mb-10">
                    <Product v-for="product in pageProducts" :product="product" :type="this.mode"></Product>
                </div>
                <div class="flex items-center mb-10">
                    <div class="mx-auto">
                        <Pagination v-model:page="page" :items="results.length" :per-page="perPage" :element-count="9"></Pagination>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="flex flex-col w-full h-64 justify-center items-center">
            <span class="sr-only">Loading...</span>
            <svg aria-hidden="true" class="size-32 mt-6 text-blue-100 animate-spin fill-blue-400" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
            </svg>
        </div>
    </div>
</template>
<script>
import Pagination from "./Pagination.vue";
import Product from "./Product.vue";
import { prepareMessage } from "../utils/message";

export default {
    components: {Pagination, Product},
    props: ['mode', 'searchString', 'uploadString'],
    data: () => ({
        loaded: false,
        products: [],
        categories: [],
        filterPrice: 'all',
        filterCategories: [],
        filterFeatures: [],
        sort: 'new',
        page: 1,
        perPage: 9,
        search: null,
        uploadHandler: null,
        productQueryHandler: null,
    }),
    computed: {
        results: {
            get() {
                return this.sortProducts((() => {
                    if (!this.products.length) {
                        return [];
                    }

                    let results = this.products;

                    if (this.search) {
                        results = results.filter((product) => {
                            return product.name.includes(this.search)
                                || product.description.includes(this.search)
                                || product.composer_package.includes(this.search);
                        });
                    }

                    if (this.filterPrice) {
                        results = results.filter((product) => this.filterProductPrice(product));
                    }

                    if (this.filterFeatures.length) {
                        results = results.filter((product) => this.filterProductFeature(product))
                    }

                    if (this.filterCategories.length) {
                        results = results.filter((product) => this.filterProductCategories(product))
                    }

                    return results;
                })());
            },
            set(value) {
                this.filter = value;
            }
        },
        pageProducts: {
            get() {
                return this.results.length > this.perPage
                    ? this.results.slice((this.page * this.perPage) - this.perPage, this.page * this.perPage)
                    : this.results;
            }
        },
        showingFromProduct: {
            get() {
                return ((this.page - 1) * this.perPage) || 1;
            }
        }
    },
    watch: {
        search: function (value) {
            if (!value) {
                this.filter = 'all';
            }
            this.page = 1;
        },
        perPage: function (value) {
            this.page = 1;
        }
    },
    mounted() {
        // Define handlers based on the mode
        this.uploadHandler = {
            plugin: 'onLoadPluginUploader',
            theme: 'onLoadThemeUploader',
        }[this.mode];

        if (this.products.length) {
            return;
        }

        this.$request('onGetMarketplaceListings', {
            success: (response) => {
                this.products = response.result.products[`${this.mode}s`];
                this.categories = response.result.categories;
                this.loaded = true;

                // The following is the most stupid thing I've ever seen, idk why but vue realllllllly doesn't like
                // `all` being the default, if somebody else wants to fix this then that would be great <3.
                this.filterPrice = 'all';
                this.filterPrice = null;
                this.$nextTick(() => {
                    this.filterPrice = 'all';
                });

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
        filterProductPrice(product) {
            switch (this.filterPrice) {
                case 'all':
                    return true;
                case 'paid':
                    return product.price > 0;
                case 'free':
                    return product.price === 0;
            }
        },
        filterProductFeature(product) {
            if (!this.filterFeatures.length) {
                return true;
            }

            for (let feature of this.filterFeatures) {
                switch (feature) {
                    case 'translate':
                        if (!product.translate_support) {
                            return false;
                        }
                        break;
                }
            }

            return true;
        },
        filterProductCategories(product) {
            if (!this.filterCategories.length) {
                return true;
            }

            for (let category of this.filterCategories) {
                if (product.categories.includes(category)) {
                    return true;
                }
            }

            return false;
        },
        sortProducts(products) {
            if (!this.sort) {
                return products;
            }

            return products.sort({
                new: (a, b) => (new Date(b.updated_at)).getTime() - (new Date(a.updated_at)).getTime(),
                downloads: (a, b) => b.downloads - a.downloads,
                stars: (a, b) => b.stars - a.stars,
            }[this.sort]);
        },
        installPopup(installKey, product) {
            this.$popup({
                size: 'large updates-app installer-popup font-sans',
                content: `
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Installing <strong>${product.product.name}</strong> by <strong>${product.product.author.name}</strong></h4>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Okay</button>
                    </div>
                `,
            });

            const popup = document.querySelector('.installer-popup .modal-body');

            const checkStatus = () => {
                this.$request('onInstallProductStatus', {
                    data: {
                        install_key: installKey,
                    },
                    success: (statusResponse) => {
                        popup.innerHTML = prepareMessage(statusResponse.data);

                        if (!statusResponse.done) {
                            return setTimeout(checkStatus, 500);
                        }

                        const store = JSON.parse(localStorage.winterInstalling);
                        store.splice(store.indexOf(installKey), 1);
                        localStorage.winterInstalling = JSON.stringify(store);

                        // This is a little hack to fix the UI post install without reload.
                        product.installing = false;
                        if (statusResponse.success) {
                            product.product.installed = true;
                            product.product.installed_ref = 'just-installed';
                            product.product.latest_ref = 'just-installed';
                        } else {
                            product.product.failedInstall = true;
                        }


                        return null;
                    },
                });
            };
            checkStatus();
        },
    }
};
</script>
