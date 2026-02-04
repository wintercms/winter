<?php
    $fieldOptions = $field->options();
?>

<!-- Dropdown -->
<?php if ($this->previewMode || $field->readOnly): ?>
    <div class="form-control" <?= $field->readOnly ? 'disabled="disabled"' : ''; ?>>
        <?= (isset($fieldOptions[$field->value])) ? e(trans($fieldOptions[$field->value])) : '' ?>
    </div>
    <input type="hidden" name="<?= $field->getName() ?>" value="<?= $field->value ?>">
<?php else:
    $emptyOption = $field->getConfig('emptyOption', $field->placeholder);
    $options = $field->getAttributes(htmlBuild:false);
    $options['id'] = $field->getId();
    $options['class'] = 'form-control custom-select';
    if ($field->getConfig('showSearch', true) === false) {
        $options['class'] .= ' select-no-search';
    }
    if ($field->getConfig('allowCustom', false)) {
        $options['class'] .= ' select-modifiable';
    }
    if ($emptyOption) {
        $options['emptyOption'] = e(trans($emptyOption));
    }
    if ($field->placeholder) {
        $options['data-placeholder'] = e(trans($field->placeholder));
    }
    foreach ($fieldOptions as $key => &$value) {
        if (is_string($value) && str_contains($value, '::')) {
            $value = e(trans($value));
        }
    }
    ?>
    <?= Form::select(
        name: $field->getName(),
        list: $fieldOptions,
        selected: $field->value,
        options: $options
    ) ?>
<?php endif ?>
