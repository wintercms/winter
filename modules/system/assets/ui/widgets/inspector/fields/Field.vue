<template>
    <div
        ref="fieldElement"
        class="field"
        :class="{ focused }"
    >
        <FieldLabel
            :label="label"
            :comment="comment"
        />

        <component
            :is="fieldComponent"
            v-bind="fieldProperties"
            :focused="focused"
            :value="value"
            :original-value="originalValue"
            :dirty="dirty"
            @input="setValue"
        />
    </div>
</template>

<script>
import FieldLabel from './FieldLabel.vue';
import CheckboxListField from './CheckboxListField.vue';
import DropdownField from './DropdownField.vue';
import TextField from './TextField.vue';

export default {
    components: {
        FieldLabel,
    },
    inject: ['focusedProperty'],
    inheritAttrs: false,
    props: {
        property: {
            type: String,
            required: true,
        },
        type: {
            type: String,
            default: 'text',
            validator(value) {
                return [
                    'text',
                    'number',
                    'email',
                    'dropdown',
                    'checkbox',
                    'checkboxlist',
                ].indexOf(value.toLowerCase()) !== -1;
            },
        },
        label: {
            type: String,
            default: '',
        },
        comment: {
            type: String,
            default: '',
        },
        value: {
            type: [String, Number, Boolean, Array, Object, Date],
            default: null,
        },
        originalValue: {
            type: [String, Number, Boolean, Array, Object, Date],
            default: null,
        },
        secondaryTitleRef: {
            type: HTMLElement,
            default: null,
        },
        secondaryContentRef: {
            type: HTMLElement,
            default: null,
        },
    },
    emits: ['input'],
    computed: {
        dirty() {
            return this.value !== null && (JSON.stringify(this.value) !== JSON.stringify(this.originalValue));
        },
        focused() {
            console.log(this.property, this.focusedProperty);
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
                    return TextField;
                case 'checkboxlist':
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
