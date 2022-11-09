<template>
    <div
        class="field-control"
        @click.capture="toggle()"
    >
        <div class="custom-checkbox nolabel">
            <input
                type="checkbox"
                value="1"
                :checked="isChecked()"
            >
            <label></label>
        </div>
    </div>
</template>

<script>
import fieldProps from './fieldProps';
import secondaryForm from '../store/secondaryForm';

export default {
    inheritAttrs: false,
    props: {
        ...fieldProps,
    },
    emits: ['input'],
    data() {
        return {
            checked: Boolean(this.value),
        };
    },
    methods: {
        toggle() {
            secondaryForm.hide();
            this.checked = !this.checked;
            this.$emit('input', this.checked);
        },
        isChecked() {
            return this.checked;
        },
    },
};
</script>

<style lang="less" scoped>
@import (reference) '../style/variables.less';

.custom-checkbox {
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
</style>
