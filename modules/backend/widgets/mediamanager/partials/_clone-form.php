<?= Form::open() ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="popup">&times;</button>
        <h4 class="modal-title"><?= e(trans('backend::lang.media.clone_popup_title')) ?></h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label><?= e(trans('backend::lang.media.clone_new_name')) ?></label>
            <input
                type="text"
                name="newName"
                value="<?= e($newName) ?>"
                class="form-control"
                autocomplete="off"
                default-focus />

            <input type="hidden" name="originalPath" value="<?= e($originalPath) ?>" />
            <input type="hidden" name="type" value="<?= e($type) ?>">
        </div>
    </div>
    <div class="modal-footer">
        <button
            type="submit"
            class="btn btn-primary">
            <?= e(trans('backend::lang.media.clone_button')) ?>
        </button>
        <button
            type="button"
            class="btn btn-default"
            data-dismiss="popup">
            <?= e(trans('backend::lang.form.cancel')) ?>
        </button>
    </div>
</form>
