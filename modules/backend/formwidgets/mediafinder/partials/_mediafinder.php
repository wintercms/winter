<?php if ($this->previewMode && !$value): ?>

    <span class="form-control" disabled="disabled"><?= e(trans('backend::lang.form.preview_no_media_message')) ?></span>

<?php else: ?>

    <?php
    switch ($mode) {
        case 'image':
            echo $this->makePartial('image_single');
            break;
        case 'video':
        case 'audio':
        case 'document':
            echo $this->makePartial('file_single', ['mode' => $mode]);
            break;
        case 'file':
        case 'all':
        default:
            echo $this->makePartial('file_single', ['mode' => 'all']);
    }
    ?>

<?php endif ?>
