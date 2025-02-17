<?php
$formController = $this->getClassExtension(\Backend\Behaviors\FormController::class);
$formConfig = $formController?->getConfig();
?>
<?php if (!empty($formConfig)): ?>
    <div data-control="toolbar">
        <a href="<?= Backend::url($formConfig->defaultRedirect) ?>" class="btn btn-primary oc-icon-caret-left">
            <?= e(trans('backend::lang.form.return_to_list')) ?>
        </a>
    </div>
<?php endif; ?>
