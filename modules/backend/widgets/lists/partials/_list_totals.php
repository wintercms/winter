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
                <?php $item = $sums[$column->columnName]; ?>
                <span>
                    <?= $item['format'] ? sprintf($item['format'], $item['sum']) : number_format($item['sum'], 0, '.', ',') ?>
                    <?php if (!is_null($item['total'])): ?>
                        (<?= $item['format'] ? sprintf($item['format'], $item['total']) : number_format($item['total'], 0, '.', ',') ?>)
                    <?php endif; ?>
                </span>
            <?php endif ?>
        </td>
    <?php endforeach ?>
    <?php if ($showSetup): ?>
        <td></td>
    <?php endif; ?>
</tr>
