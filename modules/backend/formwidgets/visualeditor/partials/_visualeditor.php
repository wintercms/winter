<!-- Rich Editor -->
<?php if ($this->previewMode): ?>
    <div class="form-control"><?= $value ?></div>
<?php else: ?>
    <div
        id="<?= $this->getId() ?>"
        class="field-visualeditor size-<?= $size ?> <?= $stretch?'layout-relative stretch':'' ?>"
        data-control="visualeditor"
    >
        <div class="visualeditor-toolbar"></div>
        <textarea
            placeholder="<?= e(trans($field->placeholder)) ?>"
            name="<?= $name ?>"
            id="<?= $this->getId('textarea') ?>"
        ><?= e($value) ?></textarea>
    </div>
<?php endif ?>
