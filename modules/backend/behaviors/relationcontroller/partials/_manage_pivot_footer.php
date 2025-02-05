<?php if ($relationManageWidget->showCheckboxes): ?>
    <button
        type="button"
        class="btn btn-primary"
        data-control="popup"
        data-handler="onRelationManageAddPivot"
        data-size="huge"
        data-dismiss="popup"
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
