<template>
    <div class="flex flex-col gap-4 bg-white p-4 shadow-sm rounded-3xl">
        <div class="relative">
            <div v-if="product.installed && product.installed_ref !== product.latest_ref"
                 class="group flex flex-row absolute right-3 top-3 bg-yellow-100 text-orange-300 rounded-full py-2 px-3 cursor-pointer gap-0 hover:gap-4 transition-[width] duration-300 items-center align-center"
            >
                <span class="opacity-0 w-6 group-hover:flex group-hover:pr-9 group-hover:opacity-100 group-hover:w-full duration-300 text-nowrap">Update available</span>
                <i class="absolute right-3 icon icon-circle-exclamation justify-self-end !mt-[-1px]"></i>
            </div>

            <div v-if="product.banner_image" class="bg-blue-100 rounded-3xl overflow-hidden aspect-video shadow-md">
                <img :src="product.banner_image" :alt="`${product.name} Banner Image`" class="aspect-video">
            </div>
            <div v-else-if="product.image" class="bg-blue-100 rounded-3xl overflow-hidden aspect-video flex h-full justify-center">
                <div class="rounded-3xl w-1/2 overflow-hidden m-auto">
                    <img :src="product.image" :alt="`${product.name} Logo`" class="aspect-square">
                </div>
            </div>
            <div v-else class="bg-blue-100 rounded-3xl overflow-hidden aspect-video flex h-full justify-center">
                <div class="flex rounded-3xl size-38 bg-blue-400 text-6xl items-center select-none cursor-default m-auto aspect-square">
                    <span class="m-auto">{{product.name.substring(0, 1)}}</span>
                </div>
            </div>
        </div>
        <div class="flex justify-between w-full mt-2">
            <div>
                <!-- @TODO: Add tippy -->
                <a :href="product.repository_url"
                   target="_blank"
                   rel="noopener"
                   :title="`${numberFormat(product.stars)} GitHub Stars`"
                   class="group !no-underline text-2xl transition-all duration-300"
                >
                    <span class="text-gray-500 group-hover:text-gray-600"><i class="icon-star transition-all duration-300 group-hover:text-yellow-400 text-yellow-400/85 mr-1"></i> {{counterNumber(product.stars)}}</span>
                </a>

                <a :href="product.packagist_url"
                   target="_blank"
                   rel="noopener"
                   :title="`${numberFormat(product.downloads)} Packagist Downloads`"
                   class="group ml-6 !no-underline text-2xl transition-all duration-300"
                >
                    <span class="text-gray-500 group-hover:text-gray-600"><i class="icon-download transition-all duration-300 group-hover:text-orange-500 text-orange-500/85 mr-1"></i> {{counterNumber(product.downloads)}}</span>
                </a>
            </div>
            <div>
                <i v-if="product.translate_support"
                    class="icon-language transition-all duration-300 hover:text-gray-900 hover:bg-blue-300 text-gray-700 bg-blue-200 p-3 rounded-full"
                   title="Supports Translations!"
                ></i>
            </div>
        </div>
        <div>
            <strong class="text-3xl">{{product.name}}</strong>
        </div>
        <div>
            <strong class="text-gray-600 font-thin" v-html="product.description"></strong>
        </div>
        <div class="flex justify-between mt-auto">
            <div class="flex flex-row">
                <div class="rounded-full overflow-hidden aspect-square size-16">
                    <img :src="product.author.image">
                </div>
                <div class="flex flex-col ml-3">
                    <small>Author</small>
                    <strong>{{product.author.name}}</strong>
                </div>
            </div>
            <div>
                <button v-if="!product.installed && !installing && product.price < 1"
                        class="btn btn-outline-success rounded-3xl"
                        v-on:click="install()"
                >
                    <i class="icon-download"></i> Install
                </button>
                <button v-else-if="!product.installed && !installing && product.price > 1"
                        class="btn btn-outline-warning rounded-3xl"
                        v-on:click="purchase()"
                >
                    <i class="icon-credit-card"></i> Purchase
                </button>

                <div v-if="installing" class="installing"></div>

                <a v-if="product.installed"
                   :href="window.location.href.replace('/install', `/details/${product.code.toLowerCase().replace('.', '-')}`)"
                   class="btn btn-outline-info"
                >
                    <i class="icon-eye"></i>  Details
                </a>
            </div>
        </div>
    </div>
</template>
<script>
import {numberFormat, counterNumber} from "../utils/numbers";

export default {
    props: ['product', 'type'],
    data: () => {
        return {
            installing: false
        }
    },
    computed: {
        window: {
            get() {
                return window;
            }
        }
    },
    methods: {
        numberFormat,
        counterNumber,
        async install() {
            this.installing = true;

            this.$request('onInstallPlugin', {
                data: {
                    package: this.product.package
                },
                success: (response) => {
                    this.$parent.installPopup(response.install_key);

                    let store = [];
                    if (localStorage.winterInstalling) {
                        store = JSON.parse(localStorage.winterInstalling);
                    }
                    store.push(response.install_key);
                    localStorage.winterInstalling = JSON.stringify(store);
                }
            });
        },
        purchase() {
            window.alert('TODO');
        }
    }
};
</script>
