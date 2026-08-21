<!-- Tel (Phone) -->
<?php
$fieldOptions = $field->options();
$hasOptions = is_array($fieldOptions) && count($fieldOptions);
$listId = $hasOptions ? $field->getId() . '-list' : null;
?>
<div class="input-group static">
    <span class="input-group-addon">
        <i class="empty wn-icon-phone"></i>
    </span>
    <?php if ($this->previewMode): ?>
        <?php if ($field->value): ?>
            <a
                href="tel:<?= e($field->value) ?>"
                target="_blank"
                rel="noopener noreferrer"
                class="form-control"
            >
                <?= e($field->value) ?>
            </a>
        <?php else: ?>
            <span class="form-control">&nbsp;</span>
        <?php endif ?>
    <?php else: ?>
        <input
            type="tel"
            id="<?= $field->getId() ?>"
            name="<?= $field->getName() ?>"
            value="<?= e($field->value) ?>"
            class="form-control"
            <?= isset($field->autocomplete) && is_string($field->autocomplete) ? 'autocomplete="' . e($field->autocomplete) . '"' : '' ?>
            <?= isset($field->maxlength) && is_numeric($field->maxlength) ? 'maxlength="' . e($field->maxlength) . '"' : '' ?>
            <?= isset($field->minlength) && is_numeric($field->minlength) ? 'minlength="' . e($field->minlength) . '"' : '' ?>
            <?= isset($field->pattern) && is_string($field->pattern) ? 'pattern="' . e($field->pattern) . '"' : '' ?>
            <?= isset($field->placeholder) && is_string($field->placeholder) ? 'placeholder="' . e(trans($field->placeholder)) . '"' : '' ?>
            <?= isset($field->size) && is_numeric($field->size) ? 'size="' . e($field->size) . '"' : '' ?>
            <?= $field->getAttributes() ?>
            <?= $listId ? 'list="' . e($listId) . '"' : '' ?>
        />
        <?php if ($hasOptions): ?>
            <datalist id="<?= e($listId) ?>">
                <?php foreach ($fieldOptions as $value => $label): ?>
                    <?php $value = is_int($value) ? $label : $value ?>
                    <option value="<?= e($value) ?>"<?= $value !== $label ? ' label="' . e(trans($label)) . '"' : '' ?>></option>
                <?php endforeach ?>
            </datalist>
        <?php endif ?>
    <?php endif ?>
</div>
