<?php
$required = $scope->config['required'] ?? false;
$emptyOption = $scope->config['emptyOption'] ?? $scope->label ?? Lang::get('backend::lang.form.select_placeholder');
$hasEmpty = !$required && $emptyOption;
$selectedValue = $scope->value ?? null;

// If required and no default, preselect first option
if ($required && $selectedValue === null && !empty($scope->options)) {
    reset($scope->options);
    $selectedValue = key($scope->options);
}
?>
<div class="filter-scope dropdown" data-scope-name="<?= e($scope->scopeName) ?>">
    <select
        class="form-control custom-select select-no-search"
        data-placeholder="<?= e($emptyOption); ?>"
        data-dropdown-auto-width="true"
        data-width="resolve"
        <?= $required ? 'data-allow-clear="false"' : ''; ?>
        name="<?= e($scope->scopeName) ?>"
    >
        <?php if ($hasEmpty): ?>
            <option value="" <?= $selectedValue === null || $selectedValue === '' ? 'selected' : '' ?>></option>
        <?php endif; ?>
        <?php foreach ($scope->options as $key => $label): ?>
            <option value="<?= e($key) ?>" <?= $selectedValue == $key ? 'selected' : '' ?>>
                <?= e($label) ?>
            </option>
        <?php endforeach ?>
    </select>
</div>
