<?php Block::put('breadcrumb') ?>
    <ul>
        <li><a href="<?= Backend::url('system/eventlogs') ?>"><?= e(trans('system::lang.event_log.menu_label')) ?></a></li>
        <li><?= e(trans($this->pageTitle)) ?></li>
    </ul>
<?php Block::endPut() ?>

<?php if (!$this->fatalError): ?>

    <div class="scoreboard">
        <div data-control="toolbar">
            <div class="scoreboard-item title-value">
                <h4><?= e(trans('system::lang.event_log.id_label')) ?></h4>
                <p>#<?= $formModel->id ?></p>
            </div>
            <div class="scoreboard-item title-value">
                <h4><?= e(trans('system::lang.event_log.level')) ?></h4>
                <p><?= $formModel->level ?></p>
            </div>
            <div class="scoreboard-item title-value">
                <h4><?= e(trans('system::lang.event_log.created_at')) ?></h4>
                <p><?= Backend::dateTime($formModel->created_at) ?></p>
            </div>
        </div>
    </div>

    <div class="layout-item stretch layout-column" style="padding-bottom: 1em;">
        <?= $this->formRenderPreview() ?>

        <p>
            <a href="<?= Backend::url('system/eventlogs') ?>" class="btn btn-default wn-icon-chevron-left">
                <?= e(trans('system::lang.event_log.return_link')) ?>
            </a>
        </p>
    </div>

<?php else: ?>

    <p class="flash-message static error"><?= e(trans($this->fatalError)) ?></p>

<?php endif ?>
