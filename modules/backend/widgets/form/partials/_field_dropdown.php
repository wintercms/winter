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
    $options = $field->getAttributes(htmlBuild:false);
    $options['id'] = $field->getId();
    $options['class'] = 'form-control custom-select';
    if ($field->getConfig('showSearch', true)) {
        $options['class'] .= ' select-no-search';
    }
    if ($field->getConfig('allowCustom', false)) {
        $options['class'] .= ' select-modifiable';
    }
    if ($emptyOption = $field->getConfig('emptyOption', $field->placeholder)) {
        $options['emptyOption'] = e(trans($emptyOption));
    }
    if ($field->placeholder) {
        $options['data-placeholder'] = e(trans($field->placeholder));
    }
?>
    <?= Form::select(name:$field->getName(), list:$fieldOptions, selected:$field->value, options:$options) ?>
<?php endif ?>
