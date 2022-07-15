<?php if ($this->previewMode && !$fileList->count()): ?>

    <span class="form-control"><?= e(trans('backend::lang.form.preview_no_files_message')) ?></span>

<?php else: ?>

    <?php switch ($displayMode) {
        case 'image-single':
            echo $this->makePartial('image_single');
            break;

        case 'image-multi':
            echo $this->makePartial('image_multi');
            break;

        case 'file-single':
            echo $this->makePartial('file_single');
            break;

        case 'file-multi':
            echo $this->makePartial('file_multi');
            break;
    } ?>

    <!-- Error template -->
    <script type="text/template" id="<?= $this->getId('errorTemplate') ?>">
        <div class="popover-head">
            <h3><?= e(trans('backend::lang.fileupload.upload_error')) ?></h3>
            <p>{{errorMsg}}</p>
            <button type="button" class="close" data-dismiss="popover" aria-hidden="true">&times;</button>
        </div>
        <div class="popover-body">
            <button class="btn btn-secondary" data-remove-file><?= e(trans('backend::lang.fileupload.remove_file')) ?></button>
        </div>
    </script>

<?php endif ?>
