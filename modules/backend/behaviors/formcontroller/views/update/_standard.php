<?php Block::put('breadcrumb') ?>
    <?= $this->makeLayoutPartial('breadcrumb') ?>
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
    <p><a href="<?= Backend::url($formConfig->defaultRedirect) ?>" class="btn btn-default"><?= e(trans('backend::lang.form.return_to_list')); ?></a></p>
<?php endif ?>
