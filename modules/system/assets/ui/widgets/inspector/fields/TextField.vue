<template>
    <div class="field-control">
        <input
            :type="type"
            :placeholder="placeholder"
            :value="thisValue"
            :class="{ dirty }"
            @input="onInput"
            @focus="hideSecondaryForm(); $emit('focus')"
            @blur="$emit('blur')"
        >
    </div>
</template>

<script>
import fieldProps from './fieldProps';
import secondaryForm from '../store/secondaryForm';

export default {
    inheritAttrs: false,
    props: {
        ...fieldProps,
    },
    emits: ['input', 'focus', 'blur'],
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
        hideSecondaryForm() {
            secondaryForm.hide();
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

    &::placeholder,
    &::-moz-placeholder {
        color: @inspector-field-placeholder-fg;
        opacity: 1;
    }

    &:placeholder-shown {
        color: @inspector-field-fg;
        font-weight: normal;
    }
}
</style>
