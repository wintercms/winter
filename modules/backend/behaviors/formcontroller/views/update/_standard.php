<?php Block::put('breadcrumb') ?>
    <?= $this->makeLayoutPartial('breadcrumb') ?>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>
    <?= Form::open([
        'class' => 'layout',
        'data-change-monitor' => 'true',
        'data-window-close-confirm' => 'true',
    ]) ?>
        <div class="layout-row">
            <?= $this->formRender() ?>
        </div>

        <div class="form-buttons p-t">
            <div class="loading-indicator-container">
                <button
                    type="button"
                    data-request="onSave"
                    data-browser-validate
                    data-load-indicator="<?= e(trans('backend::lang.form.saving')) ?>"
                    data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
                    data-request-data="redirect:0"
                    data-hotkey="ctrl+s, cmd+s"
                    class="btn btn-primary"
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
                    class="btn btn-default"
                >
                    <?= e(trans('backend::lang.form.save_and_close')); ?>
                </button>
                <button
                    type="button"
                    class="wn-icon-trash-o btn-icon danger pull-right"
                    data-request="onDelete"
                    data-load-indicator="<?= e(trans('backend::lang.form.deleting_name', ['name' => trans($formConfig->name)])); ?>"
                    data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
                    data-request-confirm="<?= e(trans('backend::lang.form.confirm_delete')); ?>">
                </button>
                <span class="btn-text">
                    <?= e(trans('backend::lang.form.or')) ?> <a href="<?= Backend::url($formConfig->defaultRedirect) ?>"><?= e(trans('backend::lang.form.cancel')); ?></a>
                </span>
            </div>
        </div>
    <?= Form::close() ?>
<?php else: ?>
    <p class="flash-message static error"><?= e($this->fatalError) ?></p>
    <p><a href="<?= Backend::url($formConfig->defaultRedirect) ?>" class="btn btn-default"><?= e(trans('backend::lang.form.return_to_list')); ?></a></p>
<?php endif ?>
