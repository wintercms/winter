<?php Block::put('breadcrumb') ?>
    <?= $this->makeLayoutPartial('breadcrumb') ?>
    <?= $this->formRenderRecordNavigation() ?>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>
    <?= Form::open([
        'id' => $this->formGetId(),
        'class' => 'layout',
        'data-change-monitor' => 'true',
        'data-window-close-confirm' => 'true',
    ]) ?>
        <div class="layout-row">
            <?= $this->formRender() ?>
        </div>

        <div class="form-buttons p-t">
            <?= $this->formMakePartial('toolbar') ?>
        </div>
    <?= Form::close() ?>
<?php else: ?>
    <p class="flash-message static error"><?= e($this->fatalError) ?></p>
    <p><a href="<?= isset($formConfig) ? Backend::url($formConfig->defaultRedirect) : 'javascript:history.back()' ?>" class="btn btn-default"><?= e(trans('backend::lang.form.return_to_list')); ?></a></p>
<?php endif ?>
