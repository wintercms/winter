<template>
    <transition name="popover-fade">
        <div
            v-if="shown"
            class="inspector popover-layout"
            :style="styles"
        >
            <div
                class="arrow"
                :style="arrowStyles"
            ></div>
            <header>
                <slot name="title"></slot>
                <slot name="description"></slot>
                <div
                    class="inspector-hide wn-icon-remove"
                    @click.stop="hideFn"
                ></div>
            </header>
        </div>
    </transition>
</template>

<script>
export default {
    props: {
        shown: {
            type: Boolean,
            default: false,
        },
        snowboard: {
            type: Object,
            required: true,
        },
        inspectedElement: {
            type: HTMLElement,
            required: true,
        },
        hideFn: {
            type: Function,
            required: true,
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
            top: 0,
            left: 0,
            inspectedElementStyle: {
                position: null,
                zIndex: null,
            },
        };
    },
    computed: {
        styles() {
            return {
                top: `${this.top}px`,
                left: `${this.left}px`,
            };
        },
        arrowStyles() {
            const elementRect = this.inspectedElement.getBoundingClientRect();
            const halfWidth = (elementRect.width / 2) - 5;

            return {
                left: `${halfWidth}px`,
            };
        },
    },
    mounted() {
        this.snowboard.overlay().show();
        this.calculatePosition();
        this.highlightInspectedElement();
    },
    unmounted() {
        this.snowboard.overlay().hide();
    },
    methods: {
        calculatePosition() {
            const elementRect = this.inspectedElement.getBoundingClientRect();

            this.top = elementRect.bottom + this.offsetY;
            this.left = elementRect.left + this.offsetX;
        },
        highlightInspectedElement() {
            this.inspectedElementStyle.position = this.inspectedElement.style.position;
            this.inspectedElementStyle.zIndex = this.inspectedElement.style.zIndex;

            this.inspectedElement.style.position = 'relative';
            this.inspectedElement.style.zIndex = 1001;
        },
    },
};
</script>

<style lang="less">
@import (reference) '../../../less/global.less';

// VARIABLES
@inpsector-popover-width: 340px;
@inspector-border-radius: @border-radius-base;

@inspector-bg: @body-bg;

@inspector-header-bg: @brand-secondary;
@inspector-header-fg: contrast(@inspector-header-bg, @color-text-title, #fff);

// STYLING
.inspector.popover-layout {
    position: absolute;
    width: @inpsector-popover-width;
    top: 0;
    left: 0;
    margin-top: 15px;
    z-index: 1001;
    box-shadow: @overlay-box-shadow;
    border-radius: @inspector-border-radius;

    .arrow {
        position: absolute;
        top: -20px;
        left: 0;
        border: 10px solid transparent;
        border-left-width: 6px;
        border-right-width: 6px;
        border-bottom-color: @inspector-header-bg;
    }

    header {
        padding: @padding-large-vertical (@padding-large-horizontal + 30px) @padding-large-vertical @padding-large-horizontal;
        background: @inspector-header-bg;
        color: @inspector-header-fg;
        border-bottom: 1px solid darken(@inspector-header-bg, 5%);
        border-top-left-radius: @inspector-border-radius;
        border-top-right-radius: @inspector-border-radius;

        .inspector-title {
            font-weight: bold;
        }

        .inspector-description {
            font-size: @font-size-small;
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
            border-top-right-radius: @inspector-border-radius;
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
}

.popover-fade-enter-active,
.popover-fade-leave-active {
    transition: opacity 175ms ease-out,
                transform 175ms ease-out;
}

.popover-fade-enter-from,
.popover-fade-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
</style>
