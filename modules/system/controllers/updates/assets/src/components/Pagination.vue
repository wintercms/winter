<template>
    <ul v-if="items && items > perPage" class="inline-flex min-w-1/3 col-span-full mx-auto xl:mx-0 lg:justify-end gap-3 !mb-0">
        <li>
            <button :class="`${hasBack || '!text-gray-400/70 bg-blue-100/90 cursor-default'} size-12 bg-blue-100 transition-all duration-300 rounded-2xl items-center flex justify-center`"
                    v-on:click="$emit('update:page', Math.max(page - 1, 1))"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            </button>
        </li>
        <li v-for="i in pages">
            <span v-if="i === '...'" class="flex items-center justify-center size-12 h-full">{{i}}</span>
            <button v-else
                    :class="`${page === i && 'cursor-default bg-blue-300'} size-12 bg-blue-100 transition-all duration-300 rounded-2xl items-center flex justify-center`"
                    v-on:click="$emit('update:page', i)"
            >
                {{i}}
            </button>
        </li>
        <li>
            <button :class="`${hasForward || '!text-gray-400/70 bg-blue-100/90 cursor-default'} size-12 bg-blue-100 transition-all duration-300 rounded-2xl items-center flex justify-center`"
                    v-on:click="$emit('update:page', Math.min(page + 1, lastPage))"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
            </button>
        </li>
    </ul>
    <div v-else class="min-w-1/3"></div>
</template>
<script>
export default {
    props: ['page', 'items', 'perPage', 'elementCount'],
    computed: {
        hasBack: {
            get() {
                return this.page > 1;
            }
        },
        hasForward: {
            get() {
                return this.page < Math.floor(this.items / this.perPage)
            }
        },
        lastPage: {
            get() {
                return Math.floor(this.items / this.perPage);
            }
        },
        pages: {
            get() {
                if (this.items.length < 2) {
                    return null;
                }

                const pages = [];
                const current = this.page;
                const last = this.lastPage;

                pages.push(1);

                let start = Math.max(current - 2, 2);
                let end = Math.min(current + 2, last - 1);
                while ((end - start + 2) < 6) {
                    if (start > 2) {
                        start--;
                    } else if (end < last - 1) {
                        end++;
                    } else {
                        break;
                    }
                }

                if (start > 2) {
                    pages.push('...');
                }

                for (let i = start; i <= end; i++) {
                    if (i !== 1 && i !== last) {
                        pages.push(i);
                    }
                }

                if (end < last - 1) {
                    pages.push('...');
                }

                pages.push(last);

                if (pages.length < this.elementCount) {
                    if (pages[pages.length - 2] === last - 1) {
                        for (let i = last - 2; pages.length < this.elementCount && i > 3; i--) {
                            if (!pages.includes(i)) {
                                pages.splice(2, 0, i);
                            }
                        }
                    } else {
                        for (let i = 2; pages.length < this.elementCount && i < last; i++) {
                            if (!pages.includes(i)) {
                                let back = 2 + (pages[pages.length - 1] === '...' ? 1 : 0);
                                pages.splice(pages.length - back, 0, i);
                            }
                        }
                    }
                }

                return pages;
            }
        }
    }
};
</script>
