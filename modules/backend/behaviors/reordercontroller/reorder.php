<?php
$formController = $this->getClassExtension(\Backend\Behaviors\FormController::class);
$formConfig = $formController?->getConfig();
?>
<?php if (!empty($formConfig)): ?>
    <?php Block::put('breadcrumb') ?>
        <ul>
            <li><a href="<?= Backend::url($formConfig->defaultRedirect) ?>"><?= e(trans($formConfig->name . '_plural')); ?></a></li>
            <li><?= e(trans('backend::lang.reorder.default_title')) ?></li>
        </ul>
    <?php Block::endPut() ?>
<?php endif; ?>
<?= $this->reorderRender() ?>
