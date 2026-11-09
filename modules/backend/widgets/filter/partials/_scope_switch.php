<!-- Switch scope -->
<?php
    $switchState = is_scalar($scope->value) ? ($scope->value ?: '0') : '0';
?>
<div
    class="filter-scope checkbox custom-checkbox is-indeterminate"
    data-scope-name="<?= $scope->scopeName ?>">
    <input type="checkbox" id="<?= $scope->getId() ?>" data-checked="<?= e($switchState) ?>" />
    <label for="<?= $scope->getId() ?>"><?= e(trans($scope->label)) ?></label>
</div>
