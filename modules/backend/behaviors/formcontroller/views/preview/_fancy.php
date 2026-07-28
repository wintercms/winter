<?php Block::put('breadcrumb') ?>
    <?= $this->makeLayoutPartial('breadcrumb') ?>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>
    <?= Form::open([
            'id' => $this->formGetId(),
            'class' => 'layout',
        ]) ?>
        <div class="layout-row form-preview">
            <?= $this->formRenderPreview() ?>
        </div>
    <?= Form::close() ?>
<?php else: ?>
    <p class="flash-message static error"><?= e($this->fatalError) ?></p>
    <p><a href="<?= isset($formConfig) ? Backend::url($formConfig->defaultRedirect) : 'javascript:history.back()' ?>" class="btn btn-default"><?= e(trans('backend::lang.form.return_to_list')); ?></a></p>
<?php endif ?>
