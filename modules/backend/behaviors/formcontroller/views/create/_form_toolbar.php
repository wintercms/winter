<div class="loading-indicator-container">
    <button
        type="submit"
        data-request="onSave"
        data-request-data="new:1"
        data-browser-validate
        data-hotkey="ctrl+shift+s, cmd+shift+s"
        data-load-indicator="<?= e(trans('backend::lang.form.creating_name', ['name' => trans($formConfig->name)])); ?>"
        data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
        class="btn btn-primary">
        <?= e(trans('backend::lang.form.create_and_new')); ?>
    </button>
    <button
        type="button"
        data-request="onSave"
        data-browser-validate
        data-hotkey="ctrl+s, cmd+s"
        data-load-indicator="<?= e(trans('backend::lang.form.creating_name', ['name' => trans($formConfig->name)])); ?>"
        data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
        class="btn btn-primary">
        <?= e(trans('backend::lang.form.create')); ?>
    </button>
    <button
        type="button"
        data-request="onSave"
        data-browser-validate
        data-request-data="close:1"
        data-hotkey="ctrl+enter, cmd+enter"
        data-load-indicator="<?= e(trans('backend::lang.form.creating_name', ['name' => trans($formConfig->name)])); ?>"
        data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
        class="btn btn-default">
        <?= e(trans('backend::lang.form.create_and_close')); ?>
    </button>
    <span class="btn-text">
        <?= e(trans('backend::lang.form.or')) ?> <a href="<?= Backend::url($formConfig->defaultRedirect) ?>"><?= e(trans('backend::lang.form.cancel')); ?></a>
    </span>
</div>
