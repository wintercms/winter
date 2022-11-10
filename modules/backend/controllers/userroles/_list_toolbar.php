<div data-control="toolbar">
    <a href="<?= Backend::url('backend/users') ?>" class="btn btn-default wn-icon-chevron-left">
        <?= e(trans('backend::lang.user.return')) ?>
    </a>
    <a href="<?= Backend::url('backend/userroles/create') ?>" class="btn btn-primary wn-icon-plus">
        <?= e(trans('backend::lang.user.role.new')) ?>
    </a>
</div>
