<?php if ($relationManageWidget->showCheckboxes): ?>
<button
    type="button"
    class="btn btn-primary"
    data-request="onRelationManageAdd"
    data-dismiss="popup"
    data-request-success="$.wn.relationBehavior.changed('<?= e($relationField) ?>', 'added')"
    data-stripe-load-indicator>
    <?= e(trans('backend::lang.relation.add_selected')) ?>
</button>
<?php endif ?>
<button
    type="button"
    class="btn btn-default"
    data-dismiss="popup">
    <?= e(trans('backend::lang.relation.cancel')) ?>
</button>
