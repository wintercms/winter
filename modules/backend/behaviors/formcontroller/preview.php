<?php
$formController = $this->getClassExtension(\Backend\Behaviors\FormController::class);
$formConfig = $formController->getConfig();
?>

<?php Block::put('breadcrumb') ?>
    <ul>
        <li><a href="<?= Backend::url($formConfig->defaultRedirect) ?>"><?= e(trans($formConfig->name . '_plural')); ?></a></li>
        <li><?= e($this->pageTitle) ?></li>
    </ul>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>

    <div class="form-preview">
        <?= $this->formRenderPreview() ?>
    </div>

<?php else: ?>

    <p class="flash-message static error"><?= e($this->fatalError) ?></p>
    <p><a href="<?= Backend::url($formConfig->defaultRedirect) ?>" class="btn btn-default"><?= e(trans('backend::lang.form.return_to_list')); ?></a></p>

<?php endif ?>
