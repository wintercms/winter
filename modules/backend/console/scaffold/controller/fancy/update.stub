<?php Block::put('breadcrumb') ?>
    <?= $this->makeLayoutPartial('breadcrumb') ?>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>
    <div class="layout fancy-layout">
        <?= Form::open([
            'id' => $this->formGetId(),
            'class' => 'layout',
            'data-change-monitor' => 'true',
            'data-window-close-confirm' => 'true',
        ]) ?>
            <div class="layout-row">
                <?= $this->formRender() ?>
            </div>
        <?= Form::close() ?>
    </div>
<?php else: ?>
    <p class="flash-message static error"><?= e($this->fatalError) ?></p>
    <p><a href="<?= Backend::url($formConfig->defaultRedirect) ?>" class="btn btn-default"><?= e(trans('backend::lang.form.return_to_list')); ?></a></p>
<?php endif ?>
