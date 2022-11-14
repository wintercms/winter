<template>
    <div
        class="inspector-wrapper"
    >
        <div
            ref="popover"
            class="main-wrapper"
        >
            <transition name="popover-fade">
                <div
                    v-if="shown"
                    class="inspector popover-layout"
                >
                    <div
                        class="arrow"
                        data-popper-arrow
                    ></div>
                    <header>
                        <slot name="title"></slot>
                        <slot name="description"></slot>
                        <div
                            class="inspector-hide wn-icon-remove"
                            @click.stop="hideFn"
                        ></div>
                    </header>
                    <main>
                        <slot name="fields"></slot>
                    </main>
                </div>
            </transition>
        </div>

        <div
            ref="secondary"
            class="secondary-wrapper"
        >
            <transition name="popover-fade">
                <div
                    v-show="showSecondaryForm"
                    class="secondary-form popover-layout"
                >
                    <div
                        class="arrow"
                        data-popper-arrow
                    ></div>
                    <header>
                        <slot name="secondaryTitle"></slot>
                        <div
                            class="secondary-form-hide wn-icon-remove"
                            @click.stop="hideSecondaryForm"
                        ></div>
                    </header>
                    <main>
                        <slot name="secondaryContent"></slot>
                    </main>
                </div>
            </transition>
        </div>
    </div>
</template>

<script>
import { createPopper } from '@popperjs/core/lib/popper-lite';
import arrow from '@popperjs/core/lib/modifiers/arrow';
import flip from '@popperjs/core/lib/modifiers/flip';
import preventOverflow from '@popperjs/core/lib/modifiers/preventOverflow';
import offset from '@popperjs/core/lib/modifiers/offset';
import secondaryForm from '../store/secondaryForm';

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
    emits: ['hide-secondary-form'],
    data() {
        return {
            top: 0,
            left: 0,
            inspectedElementStyle: {
                position: null,
                zIndex: null,
            },
            popperInstance: null,
            secondaryFormInstance: null,
            showSecondaryForm: secondaryForm.isShown(),
        };
    },
    watch: {
        /**
         * Detects if the popover is shown. If shown, show the overlay and create the popover.
         *
         * @param {Boolean} isShown
         */
        shown(isShown) {
            if (isShown) {
                this.snowboard.overlay().show();
                this.highlightInspectedElement();
                this.$nextTick(() => {
                    this.createPopper();
                });
            } else {
                this.popperInstance.destroy();
                this.snowboard.overlay().hide();
            }
        },
        /**
         * Detects if the secondary form is shown. If shown, create the popover.
         *
         * @param {Boolean} isShown
         */
        showSecondaryForm(isShown) {
            if (isShown) {
                this.$nextTick(() => {
                    this.createSecondaryPopper();
                });
            } else {
                this.secondaryFormInstance.destroy();
            }
        },
    },
    unmounted() {
        this.snowboard.overlay().hide();
    },
    methods: {
        createPopper() {
            this.popperInstance = createPopper(this.inspectedElement, this.$refs.popover, {
                modifiers: [
                    arrow,
                    flip,
                    preventOverflow,
                    {
                        ...offset,
                        options: {
                            offset: [this.offsetX, this.offsetY + 10],
                        },
                    },
                ],
            });
        },
        createSecondaryPopper() {
            this.secondaryFormInstance = createPopper(this.$refs.popover, this.$refs.secondary, {
                placement: 'right-start',
                modifiers: [
                    arrow,
                    flip,
                    preventOverflow,
                    {
                        ...offset,
                        options: {
                            offset: [0, 10],
                        },
                    },
                ],
            });
        },
        highlightInspectedElement() {
            this.inspectedElementStyle.position = this.inspectedElement.style.position;
            this.inspectedElementStyle.zIndex = this.inspectedElement.style.zIndex;

            /* eslint-disable */
            this.inspectedElement.style.position = 'relative';
            this.inspectedElement.style.zIndex = 1001;
            /* eslint-enable */
        },
        hideSecondaryForm() {
            secondaryForm.hide();
        },
    },
};
</script>

<style lang="less" scoped>
@import (reference) '../../../less/global.less';
@import (reference) '../style/variables.less';

// VARIABLES
@inpsector-popover-width: 360px;
@inspector-secondary-form-width: 280px;
@inspector-border-radius: @border-radius-base;

// STYLING
.main-wrapper,
.secondary-wrapper {
    z-index: 1002;
}

.inspector.popover-layout {
    position: relative;
    width: @inpsector-popover-width;
    box-shadow: @overlay-box-shadow;
    border-radius: @inspector-border-radius;

    .arrow,
    .arrow::before {
        position: absolute;
        width: 10px;
        height: 10px;
    }

    .arrow {
        visibility: hidden;
        z-index: 1002;
    }

    .arrow::before {
        visibility: visible;
        content: '';
        transform: rotate(45deg);
        background-color: @inspector-header-bg;
    }

    header {
        border-top-left-radius: @inspector-border-radius;
        border-top-right-radius: @inspector-border-radius;

        .inspector-hide {
            border-top-right-radius: @inspector-border-radius;
        }
    }

    main {
        border-bottom-left-radius: @inspector-border-radius;
        border-bottom-right-radius: @inspector-border-radius;
    }
}

.secondary-form.popover-layout {
    position: relative;
    width: @inspector-secondary-form-width;
    box-shadow: @overlay-box-shadow;
    border-radius: @inspector-border-radius;

    .arrow,
    .arrow::before {
        position: absolute;
        width: 10px;
        height: 10px;
    }

    .arrow {
        visibility: hidden;
        z-index: 1002;
    }

    .arrow::before {
        visibility: visible;
        content: '';
        transform: rotate(45deg);
        background-color: #fff;
    }

    header {
        border-top-left-radius: @inspector-border-radius;
        border-top-right-radius: @inspector-border-radius;

        .inspector-hide {
            border-top-right-radius: @inspector-border-radius;
        }
    }

    main {
        border-bottom-left-radius: @inspector-border-radius;
        border-bottom-right-radius: @inspector-border-radius;
    }
}

// ARROW PLACEMENT
.main-wrapper[data-popper-placement^='top'] .inspector.popover-layout .arrow,
.secondary-wrapper[data-popper-placement^='top'] .secondary-form.popover-layout .arrow {
    bottom: -5px;
}
.main-wrapper[data-popper-placement^='bottom'] .inspector.popover-layout .arrow,
.secondary-wrapper[data-popper-placement^='bottom'] .secondary-form.popover-layout .arrow {
    top: -5px;
}
.main-wrapper[data-popper-placement^='left'] .inspector.popover-layout .arrow,
.secondary-wrapper[data-popper-placement^='left'] .secondary-form.popover-layout .arrow {
    right: -5px;
}
.main-wrapper[data-popper-placement^='right'] .inspector.popover-layout .arrow,
.secondary-wrapper[data-popper-placement^='right'] .secondary-form.popover-layout .arrow {
    left: -5px;
}

// TRANSITIONS
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

.secondary-form.popover-fade-enter-from,
.secondary-form.popover-fade-leave-to {
    transform: translateX(20px);
}
</style>
