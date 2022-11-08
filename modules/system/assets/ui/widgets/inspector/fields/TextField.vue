<template>
    <div class="field-control">
        <input
            :type="type"
            :placeholder="placeholder"
            :value="thisValue"
            @input="onInput"
        >
    </div>
</template>

<script>
export default {
    inheritAttrs: false,
    props: {
        value: {
            type: String,
            default: '',
        },
        placeholder: {
            type: String,
            default: '',
        },
        type: {
            type: String,
            default: 'text',
        },
    },
    emits: ['input'],
    data() {
        return {
            thisValue: this.value,
        };
    },
    methods: {
        onInput(event) {
            this.thisValue = event.target.value;
            this.$emit('input', this.thisValue);
        },
    },
};
</script>

<style lang="less" scoped>
    input {
        appearance: none;
        -moz-appearance: textfield;
        border: none;
        padding: 0;
        margin: 0;
        outline: none;
        width: 100%;
    }
</style>
