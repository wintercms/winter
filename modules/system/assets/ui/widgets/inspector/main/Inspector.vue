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
                v-bind="field"
                :property="i"
                @input="(value) => processValue(field, value)"
            />
        </template>
    </component>
</template>

<script>
import PopoverLayout from '../layout/Popover.vue';
import Field from '../fields/Field.vue';

export default {
    components: {
        Field,
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
        valueBag: {
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
            values: {},
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
        /**
         * Processes the fields configuration.
         *
         * Applies compatibility changes for older versions of Inspector field configuration to ensure
         * that the old configuration style works.
         *
         * @param {Object} config
         */
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

            return fieldsConfig;
        },
        /**
         * Reformats an array of properties (the old style of Inspector field configuration) into
         * an object.
         *
         * @param {Array} properties
         */
        reformatProperties(properties) {
            const config = {};

            properties.forEach((property) => {
                config[property.property] = property;
                delete config[property.property].property;
            });

            return config;
        },
        /**
         * Processes a single property's value being changed.
         *
         * @param {Object} field
         * @param {any} value
         */
        processValue(field, value) {
            this.values[field.property] = value;

            if (this.valueBag) {
                /* eslint-disable-next-line */
                this.valueBag.value = JSON.stringify(this.values);
            }
        },
    },
};
</script>

<style lang="less">
// GLOBAL INSPECTOR STYLES
@import (reference) '../../../less/global.less';
@import (reference) '../style/variables.less';

.inspector {
    font-size: @inspector-font-size;

    header {
        position: relative;
        padding: @padding-large-vertical (@padding-small-horizontal + 30px) @padding-large-vertical @padding-small-horizontal;
        background: @inspector-header-bg;
        color: @inspector-header-fg;
        border-bottom: 1px solid darken(@inspector-header-bg, 8%);
        text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.22);
        z-index: 1003;

        .inspector-title {
            font-weight: @inspector-header-title-weight;
            font-size: @inspector-header-title-size;
        }

        .inspector-description {
            font-weight: @inspector-header-description-weight;
            font-size: @inspector-header-description-size;
            margin-top: -3px;
        }

        .inspector-hide {
            position: absolute;
            top: 0;
            right: 0;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            background: darken(@inspector-header-bg, 8%);
            border-bottom-left-radius: @inspector-border-radius;
            cursor: pointer;
            transition: background-color 175ms ease;

            &::before {
                transition: opacity 175ms ease;
                opacity: 0.7;
                margin-right: 0;
            }

            &:hover {
                background: darken(@inspector-header-bg, 12%);

                &::before {
                    opacity: 1;
                }
            }
        }
    }

    main {
        position: relative;
        background: @inspector-bg;
        z-index: 1003;

        .field {
            display: flex;
            flex-direction: row;
            justify-content: stretch;

            .field-label {
                position: relative;
                padding: @padding-small-vertical @padding-small-horizontal;
                flex: 3 0;
                background: @inspector-field-label-bg;
                color: @inspector-field-label-fg;
                border-right: 1px solid @inspector-field-border;

                .comment {
                    position: absolute;
                    right: @padding-small-vertical;
                    top: 50%;
                    transform: translateY(-66%);
                    color: @text-muted;
                }
            }

            .field-control {
                background: @inspector-field-bg;
                padding: @padding-small-vertical @padding-small-horizontal;
                cursor: pointer;
                flex: 5 0;
            }
        }

        .field + .field {
            border-top: 1px solid @inspector-field-border;
        }
    }
}
</style>
