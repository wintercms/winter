<div id="pluginUploadPopup">
    <?= Form::ajax('onInstallUploadedPlugin', [
        'data-popup-load-indicator' => true,
        'data-request-data' => 'type:plugin',
    ]) ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= e(trans('system::lang.plugins.upload')) ?></h4>
        </div>
        <div class="modal-body">
            <?= $packageUploadWidget->render() ?>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary"><?= e(trans('system::lang.actions.upload')) ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= e(trans('system::lang.actions.cancel')) ?></button>
        </div>
    <?= Form::close() ?>
</div>
