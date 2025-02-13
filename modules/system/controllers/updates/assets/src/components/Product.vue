<template>
    <div class="flex flex-col flex-grow">
        <div class="flex flex-grow border border-gray-200 border-b-0 rounded-t-lg bg-white p-6">
            <div class="flex w-full min-h-50 break-words">
                <div class="w-2/7">
                    <img :src="product.icon" :alt="product.name" class="rounded-md">
                </div>
                <div class="pl-6 w-5/7 max-w-5/7 h-full">
                    <p class="text-4xl text-blue-500">{{product.name}}</p>
                    <p>{{product.description}}</p>
                </div>
            </div>
        </div>
        <div class="flex justify-between border border-gray-200 border-t-0 rounded-b-lg p-6 bg-white">
            <div class="my-auto">
                <a :href="product.repository" target="_blank" rel="noopener" class="!text-gray-900 !no-underline">
                    <div :title="`${numberFormat(product.favers)} GitHub Stars`" class="bg-yellow-400/20 hover:bg-yellow-400/40 border border-yellow-500 inline p-2 mr-1 rounded-xl">
                        <span class="product-badge"><i class="icon-star"></i></span>
                        {{counterNumber(product.favers)}}
                    </div>
                </a>
                <a :href="product.url" target="_blank" rel="noopener" class="!text-gray-900 !no-underline">
                    <div :title="`${numberFormat(product.downloads)} Packagist Downloads`" class="bg-orange-400/20 hover:bg-orange-400/40 border border-orange-500 inline p-2 mx-1 rounded-xl">
                        <span class="product-badge"><i class="icon-download"></i></span>
                        {{counterNumber(product.downloads)}}
                    </div>
                </a>
            </div>
            <div class="flex-inline text-right">
                <button v-if="!product.installed && !installing"
                        class="btn btn-info"
                        @click="install()"
                >Install</button>
                <div v-if="installing" class="installing"></div>
                <i v-if="product.installed" class="icon-check-circle-o text-green-400 text-4xl" :title="`This ${type} is installed.`"></i>
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
        }
    }
};
</script>
