<template>
    <div class="border border-gray-400 rounded-lg bg-gray-100 p-6 mb-1">
        <div class="flex w-full">
            <div class="w-1/3">
                <img :src="product.icon" :alt="product.name" class="rounded-md">
            </div>
            <div class="pl-6 w-2/3 max-w-2/3">
                <p class="text-2xl text-blue-500">{{product.name}}</p>
                <p>{{product.description}}</p>
            </div>
        </div>

        <div class="flex justify-between align-center pt-5">
            <div class="my-auto">
                <a :href="product.repository" target="_blank" rel="noopener" class="!text-gray-900 !no-underline">
                    <div title="GitHub Stars" class="bg-yellow-400 hover:bg-yellow-400/70 border border-yellow-500 inline p-2 mx-1 rounded-xl">
                        <span class="product-badge"><i class="icon-star"></i></span>
                        {{product.favers}}
                    </div>
                </a>
                <a :href="product.url" target="_blank" rel="noopener" class="!text-gray-900 !no-underline">
                    <div title="Packagist Downloads" class="bg-orange-400 hover:bg-orange-400/70 border border-orange-500 inline p-2 mx-1 rounded-xl">
                        <span class="product-badge"><i class="icon-download"></i></span>
                        {{product.downloads}}
                    </div>
                </a>
            </div>
            <div class="inline text-right">
                <button v-if="!product.installed && !installing"
                        class="btn btn-info"
                        @click="install()"
                >Install</button>
                <div v-if="installing" class="installing"></div>
                <i v-if="product.installed" class="icon-check text-green-400 text-4xl" :title="`This ${type} is installed.`"></i>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['product', 'type'],
    data: () => {
        return {
            installing: false
        }
    },
    methods: {
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
