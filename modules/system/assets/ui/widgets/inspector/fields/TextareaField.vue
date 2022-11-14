<template>
    <div
        class="field-control"
        :class="{ dirty }"
        @click="showSecondary"
    >
        <span
            v-if="!value"
            class="field-value no-selections"
            v-text="placeholder"
        ></span>
        <span
            v-else
            class="field-value"
            :class="{ dirty }"
            v-text="value"
        ></span>

        <Teleport
            v-if="secondaryShown && focused"
            :to="secondaryTitleRef"
        >
            <span v-text="label"></span>
        </Teleport>

        <Teleport
            v-if="secondaryShown && focused"
            :to="secondaryContentRef"
        >
            <textarea
                ref="input"
                :placeholder="placeholder"
                :class="{ dirty }"
                rows="8"
                @input="onInput"
                @focus="$emit('focus')"
                @blur="$emit('blur')"
                v-text="thisValue"
            ></textarea>
        </Teleport>
    </div>
</template>

<script>
import fieldProps from './fieldProps';
import secondaryForm from '../store/secondaryForm';

export default {
    inheritAttrs: false,
    props: {
        ...fieldProps,
        options: {
            type: [Array, Object],
            default: () => [],
        },
    },
    emits: ['input', 'focus', 'blur'],
    data() {
        return {
            userOptions: [],
            thisValue: this.value,
            secondaryShown: secondaryForm.isShown(),
            secondaryTitleRef: secondaryForm.getTitleRef(),
            secondaryContentRef: secondaryForm.getContentRef(),
        };
    },
    watch: {
        secondaryShown(shown) {
            if (!shown && this.focused) {
                this.$emit('blur');
            }
        },
    },
    methods: {
        onInput(event) {
            this.thisValue = event.target.value;
            this.$emit('input', this.thisValue);
        },
        showSecondary() {
            secondaryForm.show();
            this.$emit('focus');
            this.$nextTick(() => {
                this.$refs.input.focus();
            });
        },
    },
};
</script>

<style lang="less" scoped>
@import (reference) '../style/variables.less';

.field-value {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    text-overflow: ellipsis;
    overflow: hidden;

    &.no-selections {
        color: @inspector-field-placeholder-fg;
    }
}

textarea {
    appearance: none;
    border: none;
    padding: 0;
    margin: 0;
    outline: none;
    width: 100%;
    color: @inspector-field-fg;
    font-weight: normal;
    resize: none;

    &::placeholder,
    &::-moz-placeholder {
        color: @inspector-field-placeholder-fg;
        opacity: 1;
    }
}
</style>
