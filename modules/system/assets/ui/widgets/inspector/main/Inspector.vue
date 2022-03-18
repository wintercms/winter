<template>
    <component
        :is="layoutComponent"
        v-bind="layoutProps"
    >
        <template #title>
            <div
                v-if="inspectorTitle"
                class="inspector-title"
                v-text="inspectorTitle"
            ></div>
        </template>
        <template #description>
            <div
                v-if="inspectorDescription"
                class="inspector-description"
                v-text="inspectorDescription"
            ></div>
        </template>
        <template #fields>
            <Field
                v-for="(field, i) in inspectorFields"
                :key="i"
            >
                <FieldLabel
                    :label="field.label"
                />
                <div class="field-control" />
            </Field>
        </template>
    </component>
</template>

<script>
import PopoverLayout from '../layout/Popover.vue';
import Field from '../fields/Field.vue';
import FieldLabel from '../fields/FieldLabel.vue';

export default {
    components: {
        Field,
        FieldLabel,
    },
    props: {
        snowboard: {
            type: Object,
            required: true,
        },
        inspectedElement: {
            type: HTMLElement,
            required: true,
        },
        form: {
            type: HTMLElement,
            default: null,
        },
        hideFn: {
            type: Function,
            required: true,
        },
        config: {
            type: [String, Object],
            default: null,
        },
        title: {
            type: String,
            required: true,
        },
        description: {
            type: String,
            default: null,
        },
        layout: {
            type: String,
            default: 'popover',
            validate(value) {
                return [
                    'popover',
                    'sidebar',
                ].indexOf(value) !== -1;
            },
        },
        placement: {
            type: String,
            default: 'auto',
            validate(value) {
                return [
                    'auto',
                    'top',
                    'right',
                    'bottom',
                    'left',
                ].indexOf(value) !== -1;
            },
        },
        fallbackPlacement: {
            type: String,
            default: 'bottom',
            validate(value) {
                return [
                    'top',
                    'right',
                    'bottom',
                    'left',
                ].indexOf(value) !== -1;
            },
        },
        offsetX: {
            type: Number,
            default: 0,
        },
        offsetY: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            showInspector: false,
            userConfig: {
                title: null,
                description: null,
                fields: null,
            },
        };
    },
    computed: {
        inspectorTitle() {
            if (this.userConfig.title) {
                return this.userConfig.title;
            }

            return this.title;
        },
        inspectorDescription() {
            if (this.userConfig.description) {
                return this.userConfig.description;
            }

            return this.description;
        },
        inspectorFields() {
            return this.userConfig.fields;
        },
        layoutProps() {
            return {
                shown: this.showInspector,
                snowboard: this.snowboard,
                inspectedElement: this.inspectedElement,
                hideFn: this.hideFn,
                placement: this.placement,
                fallbackPlacement: this.fallbackPlacement,
                offsetX: this.offsetX,
                offsetY: this.offsetY,
            };
        },
        layoutComponent() {
            return PopoverLayout;
        },
    },
    mounted() {
        this.getConfiguration();
    },
    methods: {
        /**
         * Gets the configuration of the Inspector.
         *
         * If a config is defined locally via [data-inspector-config], this is used as a default. The
         * Backend will always be queried for a configuration to determine if any overrides need to be
         * applied.
         */
        getConfiguration() {
            if (this.config) {
                const userConfig = (typeof this.config === 'string')
                    ? JSON.parse(this.config)
                    : this.config;

                if (userConfig.title) {
                    this.userConfig.title = userConfig.title;
                }
                if (userConfig.description) {
                    this.userConfig.description = userConfig.description;
                }
                this.userConfig.fields = this.processFieldsConfig(
                    userConfig.fields
                    || userConfig.properties
                    || {},
                );
            }

            this.getConfigurationFromBackend();
        },
        /**
         * Queries the backend for the final Inspector configuration.
         */
        getConfigurationFromBackend() {
            this.snowboard.request(this.form, 'onGetInspectorConfiguration', {
                success: (data) => {
                    if (data.configuration.title) {
                        this.userConfig.title = data.configuration.title;
                    }
                    if (data.configuration.description) {
                        this.userConfig.description = data.configuration.description;
                    }
                    if (data.configuration.fields) {
                        this.userConfig.fields = this.processFieldsConfig(data.configuration.fields);
                    } else if (data.configuration.properties) {
                        this.userConfig.fields = this.processFieldsConfig(data.configuration.properties);
                    }
                },
                complete: () => {
                    this.showInspector = true;
                },
            });
        },
        processFieldsConfig(config) {
            const fieldsConfig = (Array.isArray(config))
                ? this.reformatProperties(config)
                : config;

            // Post-process the fields config
            Object.entries(fieldsConfig).forEach((entry) => {
                const [, fieldConfig] = entry;

                // Rename "title" property to "label"
                if (fieldConfig.title !== undefined) {
                    if (fieldConfig.label === undefined) {
                        fieldConfig.label = fieldConfig.title;
                    }
                    delete fieldConfig.title;
                }

                if (fieldConfig.type === 'set') {
                    fieldConfig.type = 'checkboxlist';
                }
            });

            console.log(fieldsConfig);

            return fieldsConfig;
        },
        reformatProperties(properties) {
            const config = {};

            properties.forEach((property) => {
                config[property.property] = property;
                delete config[property.property].property;
            });

            return config;
        },
    },
};
</script>
