<?php
    $modelName = $formConfig->name ?? '';
?>
<?php if ($formContext === 'create'): ?>
    <div class="loading-indicator-container">
        <button
            type="submit"
            data-request="onSave"
            data-request-data="new:1"
            data-browser-validate
            data-hotkey="ctrl+shift+s, cmd+shift+s"
            data-load-indicator="<?= e(trans('backend::lang.form.creating_name', ['name' => trans($modelName)])); ?>"
            data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
            class="btn btn-primary wn-icon-plus">
            <?= e(trans('backend::lang.form.create_and_new')); ?>
        </button>
        <button
            type="button"
            data-request="onSave"
            data-browser-validate
            data-hotkey="ctrl+s, cmd+s"
            data-load-indicator="<?= e(trans('backend::lang.form.creating_name', ['name' => trans($modelName)])); ?>"
            data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
            class="btn btn-primary wn-icon-save">
            <?= e(trans('backend::lang.form.create')); ?>
        </button>
        <button
            type="button"
            data-request="onSave"
            data-browser-validate
            data-request-data="close:1"
            data-hotkey="ctrl+enter, cmd+enter"
            data-load-indicator="<?= e(trans('backend::lang.form.creating_name', ['name' => trans($modelName)])); ?>"
            data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
            class="btn btn-default wn-icon-check">
            <?= e(trans('backend::lang.form.create_and_close')); ?>
        </button>
        <span class="btn-text">
            <?= e(trans('backend::lang.form.or')) ?> <a href="<?= Backend::url($formConfig->defaultRedirect) ?>"><?= e(trans('backend::lang.form.cancel')); ?></a>
        </span>
    </div>
<?php elseif ($formContext === 'update'): ?>
    <div class="loading-indicator-container">
        <button
            type="button"
            data-request="onSave"
            data-browser-validate
            data-load-indicator="<?= e(trans('backend::lang.form.saving')) ?>"
            data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
            data-request-data="redirect:0"
            data-hotkey="ctrl+s, cmd+s"
            class="btn btn-primary wn-icon-save"
        >
            <?= e(trans('backend::lang.form.save')); ?>
        </button>
        <button
            type="button"
            data-request="onSave"
            data-browser-validate
            data-request-data="close:1"
            data-hotkey="ctrl+enter, cmd+enter"
            data-load-indicator="<?= e(trans('backend::lang.form.saving')); ?>"
            data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
            class="btn btn-default wn-icon-check"
        >
            <?= e(trans('backend::lang.form.save_and_close')); ?>
        </button>
        <button
            type="button"
            data-request="onDelete"
            data-load-indicator="<?= e(trans('backend::lang.form.deleting_name', ['name' => trans($modelName)])); ?>"
            data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
            data-request-confirm="<?= e(trans('backend::lang.form.confirm_delete')); ?>"
            class="wn-icon-trash-o btn-icon danger pull-right"
        >
        </button>
        <span class="btn-text">
            <?= e(trans('backend::lang.form.or')) ?> <a href="<?= Backend::url($formConfig->defaultRedirect) ?>"><?= e(trans('backend::lang.form.cancel')); ?></a>
        </span>
    </div>
<?php endif ?>
