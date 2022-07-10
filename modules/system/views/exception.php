<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Exception</title>
        <script src="<?= Url::asset('/modules/system/assets/vendor/prism/prism.js') ?>"></script>
        <link href="<?= Url::asset('/modules/system/assets/vendor/prism/prism.css') ?>" rel="stylesheet">
        <link href="<?= Url::asset('/modules/system/assets/css/styles.css') ?>" rel="stylesheet">
    </head>
    <body>
        <div class="container">

            <h1><i class="icon-power-off warning"></i> Error</h1>

            <p class="lead">We're sorry, but an unhandled error occurred. Please see the details below.</p>

            <div class="exception-name-block">
                <div><?= e($exception->getMessage()) ?></div>
                <p><?= $exception->getFile() ?> <span>line</span> <?= $exception->getLine() ?></p>
            </div>

            <ul class="indicators">
                <li>
                    <h3>Type</h3>
                    <p><?= e($exception->getErrorType()) ?></p>
                </li>
                <li>
                    <h3>Exception</h3>
                    <p><?= e($exception->getClassName()) ?></p>
                </li>
            </ul>

            <pre class="line-numbers language-php" data-start="<?= $exception->getHighlight()->startLine + 1 ?>" data-line="<?= $exception->getLine() ?>"><code><?php foreach ($exception->getHighlightLines() as $line): ?><?= $line ?><?php endforeach ?></code></pre>

            <h3><i class="icon-code-fork warning"></i> Stack trace</h3>

            <table class="data-table">
                <thead>
                    <tr>
                        <th class="right">#</th>
                        <th>Called Code</th>
                        <th>Document</th>
                        <th class="right">Line</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exception->getCallStack() as $stackItem): ?>
                        <tr>
                            <td class="right"><?= $stackItem->id ?></td>
                            <td>
                                <?= $stackItem->code ?>(<?php if ($stackItem->args): ?><abbr title="<?= $stackItem->args ?>">&hellip;</abbr><?php endif ?>)
                            </td>
                            <td><?= $stackItem->file ?></td>
                            <td class="right"><?= $stackItem->line ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <?php /*
        <script>
            SyntaxHighlighter.defaults['toolbar'] = false;
            SyntaxHighlighter.defaults['quick-code'] = false;
            SyntaxHighlighter.defaults['html-script'] = true;
            SyntaxHighlighter.defaults['first-line'] = <?= $exception->getHighlight()->startLine+1 ?>;
            SyntaxHighlighter.defaults['highlight'] = <?= $exception->getLine() ?>;
            SyntaxHighlighter.all()
        </script> */
        ?>
    </body>
</html>
