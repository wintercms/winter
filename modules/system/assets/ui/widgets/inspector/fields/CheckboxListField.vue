<template>
    <div
        class="field-control"
        :class="{ dirty }"
        @click="showSecondary"
    >
        <span
            v-if="!value || !value.length"
            class="field-value no-selections"
        >
            No selections
        </span>
        <span
            v-else
            class="field-value"
            v-text="selectedOptions"
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
            <div
                v-if="availableOptions.length"
                class="checkbox-list"
            >
                <div
                    v-for="(option, i) in availableOptions"
                    :key="i"
                    class="item"
                >
                    <div class="custom-checkbox nolabel">
                        <input
                            :id="`${property}-${i}`"
                            type="checkbox"
                            :value="option.value"
                            :checked="isChecked(option.value)"
                            @click.prevent="toggle(option.value)"
                        >
                        <label @click.prevent="toggle(option.value)"></label>
                    </div>
                    <div class="label">
                        <label
                            :for="`${property}-${i}`"
                            @click.prevent="toggle(option.value)"
                            v-text="option.title"
                        ></label>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script>
import fieldProps from './fieldProps';
import secondaryForm from '../store/secondaryForm';

export default {
    inject: ['snowboard', 'className'],
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
            secondaryShown: secondaryForm.isShown(),
            secondaryTitleRef: secondaryForm.getTitleRef(),
            secondaryContentRef: secondaryForm.getContentRef(),
        };
    },
    computed: {
        availableOptions() {
            if (Array.isArray(this.options) && this.options.length) {
                return this.options;
            }
            if (typeof this.options === 'object' && Object.keys(this.options).length) {
                const options = [];

                Object.entries(this.options).forEach(([key, value]) => {
                    options.push({
                        value: key,
                        title: value,
                    });
                });

                return options;
            }
            return this.userOptions;
        },
        selectedOptions() {
            if (!this.value || !this.value.length) {
                return '';
            }

            const selected = [];

            this.value.forEach((value) => {
                const option = this.availableOptions.find((availableOption) => availableOption.value === value);

                if (option) {
                    selected.push(option.title);
                }
            });

            return selected.join(', ');
        },
    },
    watch: {
        secondaryShown(shown) {
            if (!shown && this.focused) {
                this.$emit('blur');
            }
        },
    },
    mounted() {
        if (!this.availableOptions.length) {
            this.loadOptionsFromBackend();
        }
    },
    methods: {
        /**
         * Queries the backend for the final Inspector configuration.
         */
        loadOptionsFromBackend() {
            return new Promise((resolve) => {
                this.snowboard.request(this.form, 'onInspectableGetOptions', {
                    data: {
                        inspectorClassName: this.className,
                        inspectorProperty: this.property,
                    },
                    success: (data) => {
                        if (data.options) {
                            this.userOptions = data.options;
                        }
                        resolve();
                    },
                });
            });
        },
        showSecondary() {
            secondaryForm.hide();
            secondaryForm.show();
            this.$emit('focus');
        },
        toggle(value) {
            const values = this.value ?? [];
            if (values.includes(value)) {
                values.splice(values.indexOf(value), 1);
            } else {
                values.push(value);
            }
            this.$emit('input', values);
        },
        isChecked(value) {
            if (this.value === null) {
                return false;
            }

            return this.value.includes(value);
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
        font-weight: normal !important;
    }
}

.checkbox-list {
    display: flex;
    flex-direction: column;
    gap: @padding-small-vertical;

    .item {
        display: flex;
        flex-direction: row;
        gap: @padding-small-horizontal;
        align-items: center;
        justify-items: center;
    }

    .custom-checkbox {
        flex-grow: 0;
        flex-shrink: 0;
        position: relative;
        height: 20px;
        width: 20px;
        padding-left: 0;

        label {
            position: absolute;
            top: 0;
            left: 0;
            margin: 0;
            padding: 0;
        }
    }

    .label {
        flex-grow: 1;
        cursor: pointer;

        label {
            cursor: pointer;
            margin: 0;
        }
    }
}
</style>
