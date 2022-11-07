<template>
    <div
        v-if="label"
        class="field-label"
    >
        <span
            class="label"
            v-text="label"
        ></span>
        <span
            v-if="comment !== ''"
            ref="tooltip"
            class="comment icon-info-circle"
            @mouseover="showTooltip"
            @mouseout="hideTooltip"
        ></span>

        <div
            ref="popover"
            class="tooltip-wrapper"
        >
            <transition
                name="tooltip-fade"
                @transitionend="destroyTooltip"
            >
                <div
                    v-show="tooltipShown"
                    class="popover-tooltip"
                >
                    <div
                        class="arrow"
                        data-popper-arrow
                    ></div>
                    <div
                        class="content"
                        v-text="comment"
                    ></div>
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

export default {
    props: {
        label: {
            type: [String, Boolean],
            default: false,
        },
        comment: {
            type: [String],
            default: '',
        },
    },
    data() {
        return {
            tooltipShown: false,
            tooltipInstance: null,
        };
    },
    beforeUnmount() {
        if (this.tooltipInstance) {
            this.tooltipInstance.destroy();
        }
    },
    methods: {
        showTooltip() {
            // Handle tooltips that are still fading out - delete them and re-create
            if (this.tooltipInstance) {
                this.tooltipInstance.destroy();
                this.tooltipInstance = null;
            }

            this.tooltipInstance = createPopper(this.$refs.tooltip, this.$refs.popover, {
                placement: 'top',
                modifiers: [
                    arrow,
                    {
                        ...offset,
                        options: {
                            offset: [0, 5],
                        },
                    },
                ],
            });
            this.tooltipShown = true;
            this.$nextTick(() => {
                this.tooltipInstance.update();
            });
        },
        hideTooltip() {
            this.tooltipShown = false;
        },
        destroyTooltip() {
            if (!this.tooltipShown && this.tooltipInstance) {
                this.tooltipInstance.destroy();
                this.tooltipInstance = null;
            }
        },
    },
};
</script>

<style lang="less" scoped>
@import (reference) '../../../less/global.less';
@import (reference) '../style/variables.less';

.popover-tooltip {
    z-index: 1002;
    background: @tooltip-bg;
    color: @tooltip-color;
    padding: @padding-small-vertical @padding-small-horizontal;
    max-width: @tooltip-max-width;

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
        background-color: @tooltip-bg;
    }
}

// ARROW PLACEMENT
.tooltip-wrapper[data-popper-placement^='top'] .popover-tooltip .arrow {
    bottom: -5px;
}
.tooltip-wrapper[data-popper-placement^='bottom'] .popover-tooltip .arrow {
    top: -5px;
}
.tooltip-wrapper[data-popper-placement^='left'] .popover-tooltip .arrow {
    right: -5px;
}
.tooltip-wrapper[data-popper-placement^='right'] .popover-tooltip .arrow {
    left: -5px;
}

// TRANSITIONS
.tooltip-fade-enter-active,
.tooltip-fade-leave-active {
    transition: opacity 175ms ease-out;
}

.tooltip-fade-enter-from,
.tooltip-fade-leave-to {
    opacity: 0;
}
</style>
