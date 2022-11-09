// Default properties for all fields

export default {
    property: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'text',
        validator(value) {
            return [
                'string',
                'text',
                'number',
                'email',
                'dropdown',
                'checkbox',
                'set',
                'checkboxlist',
                'textarea',
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
    dirty: {
        type: Boolean,
        default: false,
    },
    focused: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        type: String,
        default: '',
    },
};
