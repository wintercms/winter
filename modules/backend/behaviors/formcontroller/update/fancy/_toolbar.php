<div class="form-buttons loading-indicator-container">
    <a
        type="submit"
        data-request="onSave"
        data-hotkey="ctrl+s, cmd+s"
        data-request-data="redirect:0"
        data-load-indicator="<?= e(trans('backend::lang.form.saving')); ?>"
        class="btn btn-primary wn-icon-check save">
        <?= e(trans('backend::lang.form.save')); ?>
    </a>
    <a
        type="button"
        class="btn btn-default wn-icon-trash-o delete"
        data-request="onDelete"
        data-load-indicator="<?= e(trans('backend::lang.form.deleting')); ?>"
        data-request-confirm="<?= e(trans('backend::lang.form.confirm_delete')); ?>">
    </a>
</div>
