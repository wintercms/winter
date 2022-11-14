import { ref } from 'vue';

const shown = ref(false);
const titleRef = ref(null);
const contentRef = ref(null);

export default {
    getTitleRef() {
        return titleRef;
    },
    setTitleRef(element) {
        titleRef.value = element;
    },
    getContentRef() {
        return contentRef;
    },
    setContentRef(element) {
        contentRef.value = element;
    },
    show() {
        shown.value = true;
    },
    hide() {
        shown.value = false;
    },
    isShown() {
        return shown;
    },
    reset() {
        shown.value = false;
        titleRef.value = null;
        contentRef.value = null;
    },
};
