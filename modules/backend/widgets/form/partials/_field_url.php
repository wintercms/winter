<!-- URL -->
<?php
$fieldOptions = $field->options();
$hasOptions = is_array($fieldOptions) && count($fieldOptions);
$listId = $hasOptions ? $field->getId() . '-list' : null;
?>
<div class="input-group static">
    <span class="input-group-addon">
        <i class="empty wn-icon-link"></i>
    </span>
    <?php if ($this->previewMode): ?>
        <?php if ($field->value): ?>
            <a
                href="<?= e($field->value) ?>"
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
            type="url"
            name="<?= $field->getName() ?>"
            id="<?= $field->getId() ?>"
            value="<?= e($field->value) ?>"
            placeholder="<?= e(trans($field->placeholder)) ?>"
            class="form-control"
            <?= $field->getAttributes() ?>
            <?= isset($field->maxlength) ? 'maxlength="' . e($field->maxlength) . '"' : '' ?>
            <?= isset($field->minlength) ? 'minlength="' . e($field->minlength) . '"' : '' ?>
            <?= isset($field->pattern) ? 'pattern="' . e($field->pattern) . '"' : '' ?>
            <?= isset($field->size) ? 'size="' . e($field->size) . '"' : '' ?>
            <?= $listId ? 'list="' . e($listId) . '"' : '' ?>
            <?= isset($field->autocomplete) ? 'autocomplete="' . e($field->autocomplete) . '"' : '' ?>
            <?= isset($field->required) && $field->required ? 'required' : '' ?>
            <?= isset($field->readonly) && $field->readonly ? 'readonly' : '' ?>
            <?= isset($field->disabled) && $field->disabled ? 'disabled' : '' ?>
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
