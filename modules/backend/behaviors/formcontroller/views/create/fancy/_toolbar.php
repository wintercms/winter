<div class="form-buttons loading-indicator-container">
    <!-- Save -->
    <a
        href="javascript:;"
        class="btn btn-primary wn-icon-check save"
        data-request="onSave"
        data-browser-validate
        data-load-indicator="<?= e(trans('backend::lang.form.saving')) ?>"
        data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
        data-hotkey="ctrl+s, cmd+s"
    >
        <?= e(trans('backend::lang.form.save')) ?>
    </a>

    <a class="btn btn-default" href="<?= $this->actionUrl('') ?>"><?= e(trans('backend::lang.form.cancel')); ?></a>
</div>
