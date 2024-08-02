<?php if ($this->previewMode): ?>
    <div class="form-control"><?= Markdown::parse(e($value)) ?></div>
<?php else: ?>
    <div
        id="<?= $this->getId() ?>"
        class="
            field-markdowneditor
            size-<?= $size ?>
            <?php if ($stretch): ?>
                layout-relative stretch
            <?php endif ?>
            <?php if ($readOnly || $disabled): ?>
                disabled
            <?php endif ?>
        "
        data-control="markdowneditor"
        data-refresh-handler="<?= $this->getEventHandler('onRefresh') ?>"
        data-view-mode="<?= $mode ?>"
        <?php if ($useMediaManager): ?>
            data-use-media-manager="true"
        <?php endif ?>
        <?php if ($readOnly || $disabled): ?>
            data-disabled="true"
        <?php endif; ?>
        <?= $this->formField->getAttributes() ?>>

        <div class="markdowneditor-container">
            <div class="markdowneditor-toolbar"></div>
            <div class="markdowneditor-editor">
                <div class="editor-container"></div>
                <input name="<?= $name ?>" data-value-bag id="<?= $this->getId('value') ?>" type="hidden" value="<?= e($value) ?>">
            </div>
        </div>
        <div class="markdowneditor-preview"></div>

    </div>
<?php endif ?>
