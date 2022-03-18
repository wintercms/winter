<template>
    <div
        v-if="label"
        class="field-label"
    >
        <span
            class="label"
            v-text="label"
        />
    </div>
</template>

<script>
export default {
    props: {
        label: {
            type: [String, Boolean],
            default: false,
        },
    },
};
</script>
