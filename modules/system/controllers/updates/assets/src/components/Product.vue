<template>
    <div class="flex flex-col gap-4 bg-white p-4 shadow-sm rounded-3xl">
        <div class="relative">
            <div v-if="product.installed && product.installed_ref !== product.latest_ref"
                 class="group flex flex-row absolute right-3 top-3 bg-yellow-100 text-orange-400/80 rounded-full py-2 px-3 cursor-pointer gap-0 hover:gap-4 transition-[width] ease-in-out duration-300 items-center align-center overflow-hidden"
            >
                <span class="opacity-0 w-6 group-hover:flex group-hover:pr-9 group-hover:opacity-100 group-hover:w-50 duration-300 text-nowrap">Update available</span>
                <i class="absolute size-6 right-0 pr-3 icon icon-circle-exclamation bg-yellow-100 p-1 justify-self-end !mt-[-1px]"></i>
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
        <div class="flex flex-wrap gap-4 justify-between mt-auto">
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

                <div v-if="installing" class="flex h-full items-center mt-1 mr-1">
                    <svg aria-hidden="true" class="size-10 text-blue-100 animate-spin fill-blue-400" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                </div>

                <a v-if="product.installed"
                   :href="window.location.href.replace('/install', `/details/${product.code.toLowerCase().replace('.', '-')}`)"
                   class="btn btn-outline-info"
                >
                    <i class="icon-eye"></i> Details
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

            this.$request('onInstallPackage', {
                data: {
                    type: this.type,
                    package: this.product.composer_package
                },
                success: (response) => {
                    this.$parent.installPopup(response.install_key, this);

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
