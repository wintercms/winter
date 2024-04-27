<?php if ($this->previewMode && !$value): ?>

    <span class="form-control" disabled="disabled"><?= e(trans('backend::lang.form.preview_no_media_message')) ?></span>

<?php else: ?>

    <?php
    switch ($mode) {
        case 'image':
        case 'video':
        case 'audio':
            echo $this->makePartial('media_single', ['mode' => $mode]);
            break;
        case 'document':
        case 'file':
        case 'all':
        default:
            echo $this->makePartial('file_single', ['mode' => $mode]);
    }
    ?>

<?php endif ?>
