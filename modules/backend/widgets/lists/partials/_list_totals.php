<tr class="table-totals">
    <?php if ($showCheckboxes): ?>
        <td></td>
    <?php endif ?>
    <?php if ($showTree): ?>
        <td class="list-tree">
            <span></span>
        </td>
    <?php endif ?>
    <?php foreach ($columns as $column): ?>
        <td>
            <?php if ($column->type == 'number' && $column->summable): ?>
                <span>
                    <?= number_format($sums[$column->columnName], 0, '.', ',') ?>
                    (<?= number_format($totalSums[$column->columnName], 0, '.', ',') ?>)
                </span>
            <?php endif ?>
        </td>
    <?php endforeach ?>
    <?php if ($showSetup): ?>
        <td></td>
    <?php endif; ?>
</tr>
