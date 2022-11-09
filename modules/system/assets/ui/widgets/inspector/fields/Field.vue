<template>
    <div
        ref="fieldElement"
        class="field"
        :class="{ focused: isFocused }"
    >
        <FieldLabel
            :label="label"
            :comment="comment"
        />

        <component
            :is="fieldComponent"
            v-bind="fieldProperties"
            :focused="isFocused"
            :dirty="isDirty"
            @input="setValue"
            @focus="$emit('focus', property)"
            @blur="$emit('blur', property)"
        />
    </div>
</template>

<script>
import fieldProps from './fieldProps';
import FieldLabel from './FieldLabel.vue';

import CheckboxListField from './CheckboxListField.vue';
import DropdownField from './DropdownField.vue';
import TextField from './TextField.vue';
import TextareaField from './TextareaField.vue';

export default {
    components: {
        FieldLabel,
    },
    inheritAttrs: false,
    props: {
        ...fieldProps,
        focusedProperty: {
            type: String,
            default: null,
        },
    },
    emits: ['input', 'focus', 'blur'],
    computed: {
        isDirty() {
            return this.value !== null && (JSON.stringify(this.value) !== JSON.stringify(this.originalValue));
        },
        isFocused() {
            return this.property === this.focusedProperty;
        },
        fieldProperties() {
            const props = { ...this.$props };

            Object.entries(this.$attrs).forEach(([key, value]) => {
                props[key] = value;
            });

            return props;
        },
        fieldComponent() {
            switch (this.type) {
                case 'text':
                case 'number':
                case 'email':
                case 'string':
                    return TextField;
                case 'textarea':
                    return TextareaField;
                case 'checkboxlist':
                case 'set':
                    return CheckboxListField;
                case 'dropdown':
                    return DropdownField;
                default:
                    throw new Error(`Invalid field type "${this.type}" for property "${this.property}"`);
            }
        },
    },
    methods: {
        setValue(value, reset) {
            this.$emit('input', value, reset);
        },
    },
};
</script>
