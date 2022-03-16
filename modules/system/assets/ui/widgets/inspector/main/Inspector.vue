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

        </template>
    </component>
</template>

<script>
import PopoverLayout from '../layout/Popover.vue';

export default {
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
            console.log(config);
        },
    },
};
</script>
