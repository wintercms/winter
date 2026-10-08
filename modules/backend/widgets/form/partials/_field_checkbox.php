<!-- Checkbox -->
<div class="checkbox custom-checkbox" tabindex="0">
<?php
/*
 * Keep the hidden fallback in sync with the visible control: a disabled
 * control must not submit its value (WHATWG HTML 4.10.22.4).
 */
$hiddenDisabled = $this->previewMode || $field->disabled || $field->hasAttribute('disabled', 'field');
?>
    <input
        type="hidden"
        name="<?= $field->getName() ?>"
        value="0"
        <?= $hiddenDisabled ? 'disabled="disabled"' : '' ?>>
    <input
        type="checkbox"
        id="<?= $field->getId() ?>"
        name="<?= $field->getName() ?>"
        value="1"
        <?= $this->previewMode ? 'disabled="disabled"' : '' ?>
        <?= $field->isSelected() ? 'checked="checked"' : '' ?>
        <?= $field->getAttributes() ?>>

    <label for="<?= $field->getId() ?>">
        <?= e(trans($field->label)) ?>
    </label>
    <?php if ($field->comment): ?>
        <p class="help-block"><?= $field->commentHtml ? trans($field->comment) : e(trans($field->comment)) ?></p>
    <?php endif ?>
</div>
