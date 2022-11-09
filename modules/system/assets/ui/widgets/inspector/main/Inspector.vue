<template>
    <component
        :is="layoutComponent"
        v-bind="layoutProps"
    >
        <template #title>
            <div
                v-if="inspectorTitle"
                ref="inspectorTitle"
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
                :value="values[i] || null"
                :original-value="originalValues[i] || null"
                :focused-property="focusedProperty"
                @input="(value) => processValue(i, value, reset)"
                @focus="(property) => setFocus(property)"
                @blur="(property) => clearFocus(property)"
            />
        </template>
        <template #secondaryTitle>
            <div
                :ref="(el) => secondaryForm.setTitleRef(el)"
                class="secondary-form-title"
            ></div>
        </template>
        <template #secondaryContent>
            <div
                :ref="(el) => secondaryForm.setContentRef(el)"
                class="secondary-form-content"
            ></div>
        </template>
    </component>
</template>

<script>
import PopoverLayout from '../layout/Popover.vue';
import Field from '../fields/Field.vue';
import secondaryForm from '../store/secondaryForm';

export default {
    components: {
        Field,
    },
    provide() {
        return {
            snowboard: this.snowboard,
            className: this.className,
        };
    },
    props: {
        /**
         * The Snowboard instance.
         */
        snowboard: {
            type: Object,
            required: true,
        },
        /**
         * The element that this inspector is attached to.
         */
        inspectedElement: {
            type: HTMLElement,
            required: true,
        },
        /**
         * The form that contains the inspected element.
         */
        form: {
            type: HTMLElement,
            default: null,
        },
        /**
         * The field that will contain the values from the Inspector.
         */
        valueBag: {
            type: HTMLElement,
            default: null,
        },
        /**
         * A function that is run upon the Inspector being closed.
         */
        hideFn: {
            type: Function,
            required: true,
        },
        /**
         * The Inspector configuration.
         */
        config: {
            type: [String, Object],
            default: null,
        },
        /**
         * The title of this Inspector.
         */
        title: {
            type: String,
            required: true,
        },
        /**
         * The description of this Inspector.
         */
        description: {
            type: String,
            default: null,
        },
        /**
         * The class name that will be used to derive options for Inspector selection fields.
         */
        className: {
            type: String,
            default: null,
        },
        /**
         * The layout that this Inspector will use.
         *
         * Can be either `popover` or `sidebar`.
         */
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
        /**
         * The placement for where this Inspector will be positioned.
         *
         * This will be dependent on the layout, but options can be either `top`, `right`, `bottom`, or `left`.
         */
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
        /**
         * The fallback placement for where this Inspector will be positioned.
         *
         * This will be dependent on the layout, but options can be either `top`, `right`, `bottom`, or `left`.
         */
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
        /**
         * The horizontal offset of this Inspector.
         */
        offsetX: {
            type: Number,
            default: 0,
        },
        /**
         * The vertical offset of this Inspector.
         */
        offsetY: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            showInspector: false,
            secondaryForm,
            userConfig: {
                title: null,
                description: null,
                fields: null,
            },
            values: {},
            originalValues: {},
            focusedProperty: null,
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
        this.setValues();
    },
    unmounted() {
        this.populateValues();
        secondaryForm.reset();
        secondaryForm.hide();
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
            } else {
                this.getConfigurationFromBackend();
            }
        },
        /**
         * Queries the backend for the final Inspector configuration.
         */
        getConfigurationFromBackend() {
            this.snowboard.request(this.form, 'onGetInspectorConfiguration', {
                data: {
                    inspectorClassName: this.className,
                },
                success: (data) => {
                    const config = (data.configuration) ? data.configuration : data;

                    if (config.title) {
                        this.userConfig.title = config.title;
                    }
                    if (config.description) {
                        this.userConfig.description = config.description;
                    }
                    if (config.fields) {
                        this.userConfig.fields = this.processFieldsConfig(config.fields);
                    } else if (config.properties) {
                        this.userConfig.fields = this.processFieldsConfig(config.properties);
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

                // Rename "description" property to "comment"
                if (fieldConfig.description !== undefined) {
                    fieldConfig.comment = fieldConfig.description;
                    delete fieldConfig.description;
                }

                // Rename "text" type to "textarea"
                if (fieldConfig.type === 'text') {
                    fieldConfig.type = 'textarea';
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
         * If the `reset` argument is true, the original value will also be set to this value,
         * effectively making the new value the "clean" value.
         *
         * @param {Object} field
         * @param {any} value
         * @param {Boolean} reset
         */
        processValue(field, value, reset) {
            this.values[field] = value;
            if (reset === true) {
                this.originalValues[field] = value;
            }
        },
        setValues() {
            if (!this.valueBag || !this.valueBag.value) {
                this.values = {};
                this.originalValues = {};
                return;
            }

            try {
                this.values = JSON.parse(this.valueBag.value);
                this.originalValues = JSON.parse(this.valueBag.value);
            } catch (e) {
                this.values = {};
                this.originalValues = {};
            }
        },
        populateValues() {
            if (this.valueBag) {
                /* eslint-disable-next-line */
                this.valueBag.value = JSON.stringify(this.values);
            }
        },
        setFocus(property) {
            this.focusedProperty = property;
        },
        clearFocus(property) {
            if (this.focusedProperty === property) {
                this.focusedProperty = null;
            }
        },
    },
};
</script>

<style lang="less">
// GLOBAL INSPECTOR STYLES
@import (reference) '../style/variables.less';

.inspector,
.secondary-form {
    font-size: @inspector-font-size;

    header {
        position: relative;
        padding: @padding-large-vertical (@padding-small-horizontal + 30px) @padding-large-vertical @padding-small-horizontal;
        background: @inspector-header-bg;
        color: @inspector-header-fg;
        border-bottom: 1px solid darken(@inspector-header-bg, 8%);
        text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.22);
        z-index: 1003;

        .inspector-title,
        .secondary-form-title {
            font-weight: @inspector-header-title-weight;
            font-size: @inspector-header-title-size;
        }

        .inspector-description,
        .secondary-form-description {
            font-weight: @inspector-header-description-weight;
            font-size: @inspector-header-description-size;
            margin-top: -3px;
        }

        .inspector-hide,
        .secondary-form-hide {
            position: absolute;
            top: 0;
            right: 0;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            background: darken(@inspector-header-bg, 8%);
            border-bottom-left-radius: @inspector-border-radius;
            border-top-right-radius: @inspector-border-radius;
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

                .comment {
                    position: absolute;
                    right: @padding-small-vertical;
                    top: 50%;
                    transform: translateY(-50%);
                    color: @text-muted;
                    padding: 5px;
                }
            }

            .field-control {
                background: @inspector-field-bg;
                padding: @padding-small-vertical @padding-small-horizontal;
                border-left: 1px solid @inspector-field-border;
                cursor: pointer;
                flex: 5 0;
                color: @inspector-field-fg;

                &.dirty {
                    color: @inspector-field-dirty-fg;
                    font-weight: bold;
                }

                &:hover {
                    position: relative;
                    z-index: 20;
                    border-left: 1px solid @inspector-field-hover-border;
                    box-shadow: 0 1px 0 @inspector-field-hover-border;
                }
            }
        }

        .field.focused .field-control {
            position: relative;
            z-index: 20;
            border-left: 1px solid @inspector-field-focus-border;
            box-shadow: 0 1px 0 @inspector-field-focus-border;
        }

        .field + .field {
            .field-label,
            .field-control {
                border-top: 1px solid @inspector-field-border;
            }

            .field-control:hover {
                border-top: 1px solid @inspector-field-hover-border;
            }

            &.focused .field-control {
                border-top: 1px solid @inspector-field-focus-border;
            }
        }

        .field:last-child .field-control:hover {
            box-shadow: none;
        }
    }
}

.secondary-form {
    header {
        background: @inspector-field-label-bg;
        color: @inspector-field-label-fg;
        border-bottom: 1px solid @inspector-field-border;

        .secondary-form-title {
            text-shadow: none;
        }

        .secondary-form-hide {
            background: darken(@inspector-field-label-bg, 8%);

            &:hover {
                background: darken(@inspector-field-label-bg, 12%);
            }
        }
    }

    main {
        padding: @padding-base-vertical @padding-base-horizontal;
        background: #fff;
    }
}
</style>
