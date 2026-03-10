<?php if ($this->previewMode): ?>
    <div class="form-control">
        <pre><?= e($value) ?></pre>
    </div>
<?php else: ?>
    <div
        id="<?= $this->getId() ?>"
        class="field-codeeditor size-<?= $size ?> <?= $stretch?'layout-relative':'' ?>"
        data-control="codeeditor"
        data-alias="<?= $this->alias ?? 'null' ?>"
        data-font-size="<?= $fontSize ?>"
        data-word-wrap="<?= $wordWrap ?>"
        data-code-folding="<?= $codeFolding ? 'true' : 'false' ?>"
        data-auto-close-tags="<?= $autoClosing ? 'true' : 'false' ?>"
        data-tab-size="<?= $tabSize ?>"
        data-theme="<?= $theme ?>"
        data-show-invisibles="<?= $showInvisibles ? 'true' : 'false' ?>"
        data-display-indent-guides="<?= $displayIndentGuides ? 'true' : 'false' ?>"
        data-show-print-margin="<?= $showPrintMargin ? 'true' : 'false' ?>"
        data-highlight-active-line="<?= $highlightActiveLine ? 'true' : 'false' ?>"
        data-use-soft-tabs="<?= $useSoftTabs ? 'true' : 'false' ?>"
        data-show-gutter="<?= $showGutter ? 'true' : 'false' ?>"
        data-read-only="<?= $readOnly ? 'true' : 'false' ?>"
        data-show-minimap="<?= $showMinimap ? 'true' : 'false' ?>"
        data-bracket-colors="<?= $bracketColors ? 'true': 'false' ?>"
        data-show-colors="<?= $showColors ? 'true' : 'false' ?>"
        data-language="<?= $language ?>"
        data-margin="<?= $margin ?>"
        data-scroll-past-end="<?= $scrollPastEnd ?>"
        <?= $this->formField->getAttributes() ?>
    >
        <div class="editor-container"></div>
        <div class="editor-toolbar" data-status-bar>
            <div class="language">
                <?= strtoupper($language) ?>
            </div>
            <div class="position">

            </div>
            <div class="actions">
                <a href="#" class="action" data-full-screen data-toggle="tooltip" title="<?= e(trans('backend::lang.editor.toggle_fullscreen')) ?>">
                    <i class="icon-maximize"></i>
                </a>
            </div>
        </div>
        <input name="<?= $name ?>" data-value-bag id="<?= $this->getId('value') ?>" type="hidden" value="<?= e($value) ?>">
    </div>
<?php endif ?>
