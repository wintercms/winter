<?php
$fieldOptions = $field->options();
$useSearch = $field->getConfig('showSearch', true);
$emptyOption = $field->getConfig('emptyOption', $field->placeholder);
?>

<!-- Dropdown -->
<select
    id="<?= $field->getId() ?>"
    name="<?= $field->getName() ?>"
    class="form-control custom-select <?= $useSearch ? '' : 'select-no-search' ?>"
    <?= $this->previewMode ? 'disabled' : '' ?>
    <?= $field->placeholder ? 'data-placeholder="'.e(trans($field->placeholder)).'"' : '' ?>
    <?= $field->getAttributes() ?>
    >
    <?php if ($emptyOption): ?>
        <option value=""><?= e(trans($emptyOption)) ?></option>
    <?php endif ?>
    <?php foreach ($fieldOptions as $value => $option): ?>
        <?php
        if (!is_array($option)) {
            $option = [$option];
        }
        ?>
        <option
            <?= $field->isSelected($value) ? 'selected="selected"' : '' ?>
            <?php if (isset($option[1])): ?>
                data-<?= strpos($option[1], '.') ? 'image' : 'icon' ?>="<?= $option[1] ?>"
            <?php endif ?>
            value="<?= e($value) ?>"
        ><?= e(trans($option[0])) ?></option>
    <?php endforeach ?>
</select>
