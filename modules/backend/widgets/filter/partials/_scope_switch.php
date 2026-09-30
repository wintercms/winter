<!-- Switch scope -->
<?php
    // The stored value is whatever was last posted to this scope, so it is only trusted to
    // be a value at all - the three states this reads back are '0', '1' and '2'.
    $switchState = is_scalar($scope->value) ? ($scope->value ?: '0') : '0';
?>
<div
    class="filter-scope checkbox custom-checkbox is-indeterminate"
    data-scope-name="<?= $scope->scopeName ?>">
    <input type="checkbox" id="<?= $scope->getId() ?>" data-checked="<?= e($switchState) ?>" />
    <label for="<?= $scope->getId() ?>"><?= e(trans($scope->label)) ?></label>
</div>
