<?php Block::put('breadcrumb') ?>
    <?= $this->makeLayoutPartial('breadcrumb') ?>
<?php Block::endPut() ?>

<?= Form::open(['class' => 'layout']) ?>

    <div class="layout-row">
        <?= $this->exportRender() ?>
    </div>

    <div class="form-buttons">
        <div class="loading-indicator-container">
            <button
                type="submit"
                data-control="popup"
                data-handler="onExportLoadForm"
                data-keyboard="false"
                class="btn btn-primary">
                <?= e(trans('backend::lang.import_export.export')) ?>
            </button>
        </div>
    </div>

<?= Form::close() ?>
