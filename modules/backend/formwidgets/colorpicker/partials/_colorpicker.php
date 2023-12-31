<div
    id="<?= $this->getId() ?>"
    class="
        field-colorpicker
        <?php if ($readOnly || $disabled || $this->previewMode): ?>
            disabled
        <?php endif; ?>
    "
    data-control="colorpicker"
    data-formats="<?= e(json_encode($formats)) ?>"
    data-available-colors="<?= e(json_encode($availableColors)) ?>"
    data-data-locker="#<?= $this->getId('input') ?>"
    <?php if ($showAlpha): ?>
        data-show-alpha="<?= $showAlpha ?>"
    <?php endif ?>
    <?php if ($allowEmpty): ?>
        data-allow-empty="<?= $allowEmpty ?>"
    <?php endif ?>
    <?php if ($allowCustom): ?>
        data-allow-custom="<?= $allowCustom ?>"
    <?php endif ?>
    <?php if ($readOnly || $disabled || $this->previewMode): ?>
        data-disabled="true"
    <?php endif; ?>
    <?= $this->formField->getAttributes() ?>
>

    <div class="colorpicker-container">
        <?php if ($readOnly || !$allowCustom || $this->previewMode): ?>
            <span
                data-color-value
                class="form-control"
            >
                <?= e($value); ?>
            </span>
        <?php else: ?>
            <input
                data-color-value
                class="form-control"
                placeholder="No color"
                value="<?= e($value); ?>"
                <?php if ($disabled): ?>
                    disabled
                <?php endif ?>
            >
        <?php endif ?>
        <div
            data-color-preview
        ></div>
    </div>

    <input
        type="hidden"
        id="<?= $this->getId('input') ?>"
        name="<?= $name ?>"
        value="<?= e($value) ?>" />
</div>
