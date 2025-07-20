<?php
$listController = $this->getClassExtension(\Backend\Behaviors\ListController::class);
$listConfig = $listController->getConfig();
?>

<div data-control="toolbar">
    <?php if ($this->isClassExtendedWith(\Backend\Behaviors\FormController::class)): ?>
        <a
            href="<?= $this->actionUrl('create') ?>"
            class="btn btn-primary wn-icon-plus">
            <?= e(trans('backend::lang.form.create_title', ['name' => trans(\Winter\Storm\Support\Str::before($listConfig->title, '_plural'))])); ?>
        </a>
    <?php endif ?>

    <?php if (isset($listConfig->showCheckboxes) && $listConfig->showCheckboxes != false): ?>
        <button
            class="btn btn-danger wn-icon-trash-o"
            disabled="disabled"
            onclick="$(this).data('request-data', { checked: $('.control-list').listWidget('getChecked') })"
            data-request="onDelete"
            data-request-confirm="<?= e(trans('backend::lang.list.delete_selected_confirm')); ?>"
            data-trigger-action="enable"
            data-trigger=".control-list input[type=checkbox]"
            data-trigger-condition="checked"
            data-request-success="$(this).prop('disabled', 'disabled')"
            data-stripe-load-indicator
        >
            <?= e(trans('backend::lang.list.delete_selected')); ?>
        </button>
    <?php endif ?>

    <?php if ($this->isClassExtendedWith(\Backend\Behaviors\ReorderController::class)): ?>
        <a
            href="<?= $this->actionUrl('reorder') ?>"
            class="btn btn-default wn-icon-arrows-up-down">
            <?= e(trans('backend::lang.reorder.reorder_title', ['name' => trans($listConfig->title)])); ?>
        </a>
    <?php endif ?>

    <?php if ($this->isClassExtendedWith(\Backend\Behaviors\ImportExportController::class)): ?>
        <div class="btn-group">
            <?php $importExport = $this->asExtension(\Backend\Behaviors\ImportExportController::class); ?>
            <?php if ($importExport->userHasAccess('export')): ?>
                <a
                    href="<?= $this->actionUrl('export') ?>"
                    class="btn btn-default wn-icon-download">
                    <?= e(trans('backend::lang.import_export.export')) ?>
                </a>
            <?php endif ?>
            <?php if ($importExport->userHasAccess('import')): ?>
                <a
                    href="<?= $this->actionUrl('import') ?>"
                    class="btn btn-default wn-icon-upload">
                    <?= e(trans('backend::lang.import_export.import')) ?>
                </a>
            <?php endif ?>
        </div>
    <?php endif ?>
</div>
