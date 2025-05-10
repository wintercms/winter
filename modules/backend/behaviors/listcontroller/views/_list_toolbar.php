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
            class="btn btn-default wn-icon-sitemap">
            <?= e(trans('backend::lang.reorder.reorder_title', ['name' => trans($listConfig->title)])); ?>
        </a>
    <?php endif ?>
</div>
