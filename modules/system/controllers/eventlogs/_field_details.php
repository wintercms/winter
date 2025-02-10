<?php
if (!isset($value['logVersion']) || $value['logVersion'] !== 2) {
    return;
}

function phpHighlight(string $str): string
{
    $transform = [
        'control' => '/\b(for|foreach|while|class|extends|implements|try|catch|finally|function|return|unset|static|public|protected|private|count|global|if|else|else if|intval|int|array)\b/',
        'bool' => '/(\bnull\b|\btrue\b|\bfalse\b)/',
        'string' => '/(&#039;\w*&#039;)/',
        'bracket' => '/(\(|\)|\[|\]|\{|\})/',
        'variable' => '/(\$[a-z]\w*)/',
        'operator' => '/( \! | \!\= | \!== | = | == | === | > | >= | < | <= | and | or )/',
    ];

    foreach ($transform as $label => $regex) {
        $str = preg_replace($regex, '<span class="' . $label . '">$1</span>', $str);
    }

    return $str;
}

function makeSnippet(array $snippet, ?int $highlight = null): string
{
    return implode(
        "\n",
        array_reduce(
            array_keys($snippet),
            function (array $carry, $key) use ($snippet, $highlight) {
                $line = $key + 1;
                $carry[] = sprintf(
                    '<div class="preview-line%s"><span class="line-number">%s</span>: %s</div>',
                    ($line === $highlight ? ' highlight' : ''),
                    $line,
                    phpHighlight(e($snippet[$key], true))
                );
                return $carry;
            },
            []
        )
    );
}

function getOrderedExceptionList(array $value): array
{
    $exceptions = [$value];
    $current = $value;
    while (isset($current['previous']) && $current['previous']) {
        $current = $current['previous'];
        $exceptions[] = $current;
    }

    return array_reverse($exceptions);
}
?>
<style>
    div.plugin-exception-beautifier  span.beautifier-message-container {
        display: none;
    }
    #winter-log-viewer h1 {
        margin-top: 0;
    }
    #winter-log-viewer .exception:not(:first-child) {
        margin-top: 25px;
    }
    p.message-log {
        font-family: monospace;
        margin: 15px auto;
    }
    div.snippet-preview-container {
        overflow-x: auto;
        background: #f5f5f5;
        margin-top: 15px;
        border-radius: 4px;
    }
    div.snippet-preview {
        line-height: 0.7em;
        width: fit-content;
        min-width: 100%;
        padding-bottom: 5px;
        white-space: pre;
        font-family: monospace, monospace;
    }
    div.snippet-preview div.preview-line {
        display: block;
        box-sizing: border-box;
        background: #f5f5f5;
        width: 100%;
        padding: 7px 10px;
        margin: -5px 0;
    }
    div.snippet-preview div.preview-line:first-child {
        margin-top: -18px;
    }
    div.snippet-preview div.preview-line:last-child {
        padding-bottom: 0;
    }
    div.snippet-preview div.preview-line.highlight {
        display: block;
        background: #fff;
        padding: 5px 10px;
        margin: -5px 0;
    }
    div.snippet-preview div.preview-line.highlight span.line-number {
        color: red;
    }
    div.snippet-preview span.bracket { color: #343434; }
    div.snippet-preview span.variable { color: #d3542f; }
    div.snippet-preview span.operator { color: #055c9b; }
    div.snippet-preview span.control { color: #8e09e1; }
    div.snippet-preview span.string { color: #6a8d00; }
    div.snippet-preview span.bool { color: #096ee1; }
    .trace-title {
        margin: 15px auto;
        display: block;
        font-weight: bold;
    }
    .trace {
        border: 1px solid #dcdcdc;
        border-radius: 6px;
        margin-top: 15px;
    }
    .trace-frame {
        background: #efefef;
        padding: 10px;
    }
    .trace-frame:first-child {
        border-top-right-radius: 6px;
        border-top-left-radius: 6px;
    }
    .trace-frame:last-child {
        border-bottom-right-radius: 6px;
        border-bottom-left-radius: 6px;
    }
    .trace-frame:not(:last-child) {
        border-bottom: 1px solid #dcdcdc;
    }
    .trace-frame .label {
        cursor: pointer;
        width: 100%;
        font-size: 0.95em;
    }
    .trace-frame .label .item {
        font-weight: bold;
        font-style: italic;
    }
    .trace-frame .label  .app-icon{
        background: #a4e9ff;
        border-radius: 6px;
        padding: 3px;
        float: right;
        margin-top: -5px;
    }
    .trace-frame .folded {
        display: none;
    }
</style>
<div id="winter-log-viewer">
    <div class="formatted">
        <?php foreach (getOrderedExceptionList($value) as $index => $exception): ?>
            <div class="exception">
                <h1><?= $exception['type'] ?></h1>
                <p class="message-log"><?= $exception['message'] ?></p>

                <div>
                    <div class="trace-frame">
                        <div class="label">
                            <span class="item"><?= $exception['file'] ?></span>
                            at line <span class="item"><?= $exception['line'] ?></span>
                        </div>
                        <?php if ($exception['snippet']): ?>
                            <div class="snippet-preview-container">
                                <div class="snippet-preview">
                                    <?= makeSnippet($exception['snippet'], $exception['line']) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <span class="trace-title">Stack Trace</span>
                    <div class="trace">
                        <?php foreach ($exception['trace'] as $traceIndex => $frame): ?>
                            <div class="trace-frame">
                                <div class="label">
                                    <span class="item">#<?= $traceIndex ?> <?= $frame['file'] ?></span>
                                    in <span class="item"><?= $frame['class'] && !str_contains($frame['function'], '{') ? $frame['class'] . '::' : '' ?><?= $frame['function'] ?></span>
                                    at line <span class="item"><?= $frame['line'] ?></span>
                                    <?php if ($frame['arguments']): ?>
                                        with argument<?= count($frame['arguments']) > 1 ? 's' : '' ?>: (<span class="item"><?= implode('</span>, <span class="item">', $frame['arguments']) ?></span>)
                                    <?php endif; ?>
                                    <?php if ($frame['in_app']): ?>
                                        <span class="app-icon">In App</span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($frame['snippet']): ?>
                                    <div class="snippet-preview-container <?= $frame['in_app'] ? 'unfolded' : 'folded' ?>">
                                        <div class="snippet-preview">
                                            <?= makeSnippet($frame['snippet'], $frame['line']) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="raw" style="display: none">
        <pre class="beautifier-raw-content"><?= $value['stringTrace'] ?></pre>
    </div>
</div>

<script>
    (() => {
        document.querySelectorAll('.trace-frame').forEach((frame) => {
            frame.querySelector('.label').addEventListener('click', () => {
                frame.querySelector('div.snippet-preview-container')?.classList.toggle('folded');
            });
        });
        window.addEventListener('load', () => {
            document.querySelector('.plugin-exception-beautifier a[href="#beautifier-tab-formatted"]').addEventListener('click', () => {
                document.querySelector('#winter-log-viewer .formatted').style.display = "block";
                document.querySelector('#winter-log-viewer .raw').style.display = "none";
            });
            document.querySelector('.plugin-exception-beautifier a[href="#beautifier-tab-raw"]').addEventListener('click', () => {
                document.querySelector('#winter-log-viewer .formatted').style.display = "none";
                document.querySelector('#winter-log-viewer .raw').style.display = "block";
            });
        });
    })();
</script>
