<?php Block::put('breadcrumb') ?>
    <?= $this->makeLayoutPartial('breadcrumb') ?>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>
    <?php Block::put('form-contents') ?>
        <div class="layout">
            <div class="layout-row">
                <?= $this->formRenderOutsideFields() ?>
                <?= $this->formRenderPrimaryTabs() ?>
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
                        data-request="onDelete"
                        data-load-indicator="<?= e(trans('backend::lang.form.deleting_name', ['name' => trans($formConfig->name)])); ?>"
                        data-request-before-update="$el.trigger('unchange.oc.changeMonitor')"
                        data-request-confirm="<?= e(trans('backend::lang.form.confirm_delete')); ?>"
                        class="wn-icon-trash-o btn-icon danger pull-right"
                    >
                    </button>

                    <span class="btn-text">
                        <?= e(trans('backend::lang.form.or')) ?> <a href="<?= Backend::url($formConfig->defaultRedirect) ?>"><?= e(trans('backend::lang.form.cancel')); ?></a>
                    </span>
                </div>
            </div>
        </div>
    <?php Block::endPut() ?>

    <?php Block::put('form-sidebar') ?>
        <div class="hide-tabs"><?= $this->formRenderSecondaryTabs() ?></div>
    <?php Block::endPut() ?>

    <?php Block::put('body') ?>
        <?= Form::open([
            'class' => 'layout stretch',
            'data-change-monitor' => 'true',
            'data-window-close-confirm' => 'true',
        ]) ?>
            <?= $this->makeLayout('form-with-sidebar') ?>
        <?= Form::close() ?>
    <?php Block::endPut() ?>
<?php else: ?>
    <div class="control-breadcrumb">
        <?= Block::placeholder('breadcrumb') ?>
    </div>
    <div class="padded-container">
        <p class="flash-message static error"><?= e(trans($this->fatalError)) ?></p>
        <p><a href="<?= Backend::url($formConfig->defaultRedirect) ?>" class="btn btn-default"><?= e(trans('backend::lang.form.return_to_list')); ?></a></p>
    </div>
<?php endif ?>
