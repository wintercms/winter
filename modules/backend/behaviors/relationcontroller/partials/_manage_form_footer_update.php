<?php if ($this->readOnly): ?>
    <button
        type="button"
        class="btn btn-default"
        data-dismiss="popup">
        <?= e(trans('backend::lang.relation.close')) ?>
    </button>
<?php else: ?>
    <button
        type="submit"
        class="btn btn-primary">
        <?= e(trans('backend::lang.relation.update')) ?>
    </button>
    <button
        type="button"
        class="btn btn-default"
        data-dismiss="popup">
        <?= e(trans('backend::lang.relation.cancel')) ?>
    </button>
<?php endif ?>
