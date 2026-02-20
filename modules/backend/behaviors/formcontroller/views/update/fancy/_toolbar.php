<div class="form-buttons loading-indicator-container">
    <!-- Save -->
    <a
        href="javascript:;"
        class="btn btn-primary wn-icon-save save"
        data-request="onSave"
        data-browser-validate
        data-load-indicator="<?= e(trans('backend::lang.form.saving')) ?>"
        data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
        data-request-data="redirect:0"
        data-hotkey="ctrl+s, cmd+s"
    >
        <?= e(trans('backend::lang.form.save')) ?>
    </a>

    <!-- Save and Close -->
    <a
        href="javascript:;"
        class="btn btn-primary wn-icon-check save"
        data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
        data-request="onSave"
        data-browser-validate
        data-load-indicator="<?= e(trans('backend::lang.form.saving')) ?>"
    >
        <?= e(trans('backend::lang.form.save_and_close')) ?>
    </a>

    <?php if ($formModel->url): ?>
        <!-- Preview -->
        <a
            href="<?= e($formModel->url) ?>"
            target="_blank"
            class="btn btn-primary wn-icon-crosshairs"
            data-control="preview-button"
        >
            <?= e(trans('backend::lang.form.preview')) ?>
        </a>
    <?php endif ?>

    <a class="btn btn-default wn-icon-ban" href="<?= $this->actionUrl('') ?>"><?= e(trans('backend::lang.form.cancel')); ?></a>

    <!-- Delete -->
    <button
        type="button"
        class="btn btn-default empty wn-icon-trash-o"
        data-request="onDelete"
        title="<?= e(trans('backend::lang.form.delete')); ?>"
        data-load-indicator="<?= e(trans('backend::lang.form.deleting')); ?>"
        data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
        data-request-confirm="<?= e(trans('backend::lang.form.confirm_delete')); ?>"
        data-control="delete-button"
    ></button>
</div>
