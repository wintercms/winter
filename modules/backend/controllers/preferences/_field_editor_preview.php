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

<p class="help-block">
    Preview language:
    <a href="#" data-switch-lang="css">CSS</a> |
    <a href="#" data-switch-lang="html">HTML</a> |
    <a href="#" data-switch-lang="javascript">JavaScript</a> |
    <a href="#" data-switch-lang="twig">Twig</a> |
    <a href="#" data-switch-lang="php">PHP</a>
</p>

<script type="x-template" data-lang-snippet="css">
form, fieldset, h5, h6, pre, blockquote, ol, dl, dt, dd, address, dd, dtm, div, td, th, hr {
    margin: 0;
    padding: 0;
}

/* This is a comment */
body {
    background-color: white;
    font: 62.5% Helvetica, Arial, Tahoma, Verdana, Helvetica, sans-serif;
}

p {
    font-size: 12px;
}

strong {
    font-weight: bold;
}

span.alert {
    color: #ff0000;
    border: 1px solid #ff0000;
    padding: 2rem;
}
</script>

<script type="x-template" data-lang-snippet="html">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Winter CMS</title>
</head>

<body>
    <h1>Winter CMS</h1>
    <!-- The number one platform for content management -->

    <div class="container">
        <p style="font-weight: bold">Winter CMS is a free, open-source, self-hosted CMS platform based on the Laravel PHP Framework.</p>
    </div>
</body>
</html>
</script>

<script type="x-template" data-lang-snippet="javascript">
((Snowboard) => {
    class WinterCms extends Snowboard.PluginBase {

        // Create a new function
        myFunction(arg) {
            arg = arg.toLowerCase();

            return arg;
        }

        counter() {
            let count = 0;
            count = count + 1;

            return count;
        }
    }
})(winter.Snowboard)
</script>

<script type="x-template" data-lang-snippet="twig">
<h1>My Winter Page - {{ title }}</h1>

{% if show %}
    <p>Winter is coming!</p>
{% else %}
    <p>Winter is not coming!</p>
{% endif %}

{% for item in items %}
    <p>{{ item }}</p>
{% endfor %}
</script>

<script type="x-template" data-lang-snippet="php">
<?php echo '<?php

namespace Winter;

use \Package\AnotherClass as BaseClass;

/**
 * This is Winter CMS.
 *
 * @author Winter CMS maintainers
 */
class Winter extends Laravel implements Simplicity
{
    const NAME = \'Winter CMS\';

    public string $site = \'My site\';

    // You can construct anything
    public function __construct($site)
    {
        $this->site = $site;
        $this->begin();
    }

    protected function begin()
    {
        $this->achieveAwesome();
    }
}'; ?>
</script>
