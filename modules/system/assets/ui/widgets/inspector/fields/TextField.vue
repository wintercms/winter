<template>
    <div class="field-control">
        <input
            :type="type"
            :placeholder="placeholder"
            :value="thisValue"
            :class="{ dirty }"
            @input="onInput"
            @focus="setFocus(this)"
            @blur="clearFocus(this)"
        >
    </div>
</template>

<script>
export default {
    inject: ['setFocus', 'clearFocus'],
    inheritAttrs: false,
    props: {
        property: {
            type: String,
            required: true,
        },
        value: {
            type: String,
            default: '',
        },
        dirty: {
            type: Boolean,
            default: false,
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
@import (reference) '../../../less/global.less';
@import (reference) '../style/variables.less';

input {
    appearance: none;
    -moz-appearance: textfield;
    border: none;
    padding: 0;
    margin: 0;
    outline: none;
    width: 100%;
    color: @inspector-field-fg;
    font-weight: normal;

    &.dirty {
        font-weight: bold;
        color: @inspector-field-dirty-fg;
    }

    &:placeholder-shown {
        color: @inspector-field-fg;
        font-weight: normal;
    }
}
</style>
