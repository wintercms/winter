<div
    class="filter-scope button-group"
    data-scope-name="<?= e($scope->scopeName) ?>"
    <?= ($scope->config['required'] ?? false) ? 'data-scope-required="true"' : '' ?>
>
    <?php foreach ($scope->options as $key => $label): ?>
        <button
            class="btn <?= $scope->value === $key ? 'btn-primary' : 'btn-default' ?>"
            data-scope-name="<?= e($scope->scopeName) ?>"
            data-scope-value="<?= e($key) ?>">
            <?= e($label) ?>
        </button>
    <?php endforeach ?>
</div>
