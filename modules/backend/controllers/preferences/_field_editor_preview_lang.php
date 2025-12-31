<p class="help-block" style="margin-bottom: -15px">
    <?= e(trans('backend::lang.editor.preview')) ?>:
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
