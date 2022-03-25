<template>
    <div
        v-if="label"
        class="field-label"
    >
        <span
            class="label"
            v-text="label"
        />
        <span
            v-if="comment !== ''"
            class="comment icon-info-circle"
            :data-tooltip="comment"
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
        comment: {
            type: [String],
            default: '',
        },
    },
};
</script>
