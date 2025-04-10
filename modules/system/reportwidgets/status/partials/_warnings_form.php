<div class="modal-header">
    <button type="button" class="close" data-dismiss="popup" aria-hidden="true">&times;</button>
    <h4 class="modal-title"><?= e(trans('backend::lang.warnings.tips')) ?></h4>
</div>
<div class="modal-body">
    <?php if (count($warnings)): ?>
        <p><?= e(trans('backend::lang.warnings.tips_description')) ?></p>
        <ul>
            <?php foreach ($warnings as $warning): ?>
                <li>
                    <?= $warning['message'] ?>
                    <?php if (isset($warning['fixUrl'])): ?>
                        (<a href="<?= $warning['fixUrl'] ?>" target="winter-fix"><?= e(trans('backend::lang.warnings.how_to_fix')) ?></a>)
                    <?php endif ?>
                </li>
            <?php endforeach ?>
        </ul>
    <?php else: ?>
        <p>No warnings to display</p>
    <?php endif ?>
</div>
<div class="modal-footer">
    <button
        type="button"
        class="btn btn-default"
        data-dismiss="popup">
        <?= e(trans('backend::lang.form.close')) ?>
    </button>
</div>
