<div
      id="editorpreferencesCodeeditor"
      class="field-codeeditor size-large"
      data-control="codeeditor"
      data-alias="null"
      data-font-size="<?= $model->editor_font_size ?>"
      data-word-wrap="<?= $model->editor_word_wrap ?>"
      data-code-folding="<?= ($model->editor_enable_folding ?? ($model->editor_code_folding !== 'manual')) ? 'true' : 'false' ?>"
      data-tab-size="<?= $model->editor_tab_size ?>"
      data-theme="<?= $model->editor_theme ?>"
      data-show-invisibles="<?= $model->editor_show_invisibles ? 'true' : 'false' ?>"
      data-highlight-active-line="<?= $model->editor_highlight_active_line ? 'true' : 'false' ?>"
      data-use-soft-tabs="<?= !$model->editor_use_hard_tabs ? 'true' : 'false' ?>"
      data-display-indent-guides="<?= $model->editor_display_indent_guides ? 'true' : 'false' ?>"
      data-show-print-margin="<?= $model->editor_show_print_margin ? 'true' : 'false' ?>"
      data-show-gutter="<?= $model->editor_show_gutter ? 'true' : 'false' ?>"
      data-show-minimap="<?= $model->editor_show_minimap ? 'true' : 'false' ?>"
      data-bracket-colors="<?= $model->editor_bracket_colors ? 'true': 'false' ?>"
      data-show-colors="<?= $model->editor_show_colors ? 'true' : 'false' ?>"
      data-language="css"
      data-margin="0"
>
      <div class="editor-container"></div>
      <input type="hidden" name="editorpreferences_codeeditor" data-value-bag value="<?= e($this->makePartial('example_code')) ?>">
</div>
