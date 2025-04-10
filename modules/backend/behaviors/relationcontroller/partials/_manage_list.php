<div id="relationManagePopup" data-request-data="_relation_field: '<?= $relationField ?>', _relation_mode: 'list'">
    <?= Form::open() ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="popup">&times;</button>
            <h4 class="modal-title"><?= e(trans($relationManageTitle, [
                'name' => trans($relationLabel)
            ])) ?></h4>
        </div>

        <div class="list-flush">
            <?php if ($relationSearchWidget): ?>
                <?= $relationSearchWidget->render() ?>
            <?php endif ?>
            <?php if ($relationManageFilterWidget): ?>
                <?= $relationManageFilterWidget->render() ?>
            <?php endif ?>
            <?= $relationManageWidget->render() ?>
        </div>

        <div class="modal-footer">
            <?= $this->relationMakePartial('manage_list_footer') ?>
        </div>
    <?= Form::close() ?>
</div>
