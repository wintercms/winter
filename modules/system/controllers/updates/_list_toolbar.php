<div data-control="toolbar">
    <a
        href="javascript:;"
        class="btn btn-primary wn-icon-refresh"
        data-control="popup"
        data-handler="onLoadUpdates">
        <?= e(trans('system::lang.updates.check_label')) ?>
    </a>
    <a
        href="<?= Backend::url('system/updates/install') ?>"
        class="btn btn-success wn-icon-plus">
        <?= e(trans('system::lang.plugins.install')) ?>
    </a>
    <a
        href="<?= Backend::url('system/updates/install/themes') ?>"
        class="btn btn-success wn-icon-plus">
        <?= e(trans('system::lang.themes.install')) ?>
    </a>
    <a
        href="<?= Backend::url('system/updates/manage') ?>"
        class="btn btn-default wn-icon-puzzle-piece">
        <?= e(trans('system::lang.plugins.manage')) ?>
    </a>
</div>
