<template>
    <div class="field">
        <FieldLabel
            :label="label"
            :comment="comment"
        />

        <component
            :is="fieldComponent"
            v-bind="$props"
            :value="value"
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
    },
    emits: ['input'],
    computed: {
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
        setValue(value) {
            this.$emit('input', value);
        },
    },
};
</script>
