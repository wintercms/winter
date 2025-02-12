<?php
if (!isset($value['logVersion']) || $value['logVersion'] !== 2) {
    return;
}

/**
 * Highlights a line of php code with php syntax highlighting
 *
 * @param string $str
 * @return string
 */
function phpSyntaxHighlight(string $str): string
{
    $regexes = [
        'control' => '/\b(for|foreach|while|class |extends|yield from|yield|echo|fn|implements|try|catch|finally|throw|new|instanceof|parent|function|return|unset|static|public|protected|private|count|global|if|else|else if|intval|int|array)\b/',
        'bool' => '/(\bnull\b|\btrue\b|\bfalse\b)/',
        'string' => [
            'pattern' => '/(\221[^\221]*\221|\222[^\222]*\222)/',
            'before' => fn ($s) => str_replace('&#039;', "\221", str_replace('&quot;', "\222", $s)),
            'after' => fn ($s) => str_replace("\221", '&#039;', str_replace("\222", '&quot;', $s)),
        ],
        'number' => [
            'pattern' => '/(=\(\s)?(\d+)(?=(\s|;|,|\)|=))/',
            'replace' => '$2',
            'before' => fn ($s) => str_replace('&#039;', '\'', $s),
            'after' => fn ($s) => str_replace('\'', '&#039;', $s),
        ],
        'bracket' => '/(\(|\)|\[|\]|\{|\})/',
        'variable' => '/(\$[a-z]\w*)/',
    ];

    if (preg_match('/(^\s*?\*|^\s*?\*\/|^\s*?\/\*|^\s*?\/\/|^\s*?#)/', $str)) {
        return sprintf('<span class="comment">%s</span>', $str);
    }

    foreach ($regexes as $label => $regex) {
        if (is_string($regex)) {
            $str = preg_replace($regex, '<span class="' . $label . '">$1</span>', $str);
            continue;
        }

        $str = preg_replace(
            $regex['pattern'],
            sprintf('<span class="%s">%s</span>', $label, $regex['replace'] ?? '$1'),
            isset($regex['before']) ? $regex['before']($str) : $str
        );

        $str = isset($regex['after']) ? $regex['after']($str) : $str;
    }

    return $str;
}

/**
 * Converts an array of lines into a html snippet of code
 *
 * @param array $snippet
 * @param int|null $highlight
 * @return string
 */
function makeSnippet(array $snippet, ?int $highlight = null): string
{
    return implode(
        "\n",
        array_reduce(
            array_keys($snippet),
            function (array $carry, $key) use ($snippet, $highlight) {
                $carry[] = sprintf(
                    '<div class="preview-line%s"><span class="line-number">%s</span>: %s</div>',
                    ($key + 1 === $highlight ? ' highlight' : ''),
                    $key + 1,
                    phpSyntaxHighlight(e($snippet[$key], true))
                );
                return $carry;
            },
            []
        )
    );
}

/**
 * Gets all exceptions in the stack and returns them bottom up
 *
 * @param array $value
 * @return array
 */
function getOrderedExceptionList(array $value): array
{
    $exceptions = [$value];
    $current = $value;
    while (isset($current['previous']) && ($current = $current['previous'])) {
        $exceptions[] = $current;
    }

    return array_reverse($exceptions);
}
?>
<style>
    div.plugin-exception-beautifier  span.beautifier-message-container {
        display: none;
    }
    #winter-log-viewer {
        background: #fff;
        margin: -20px;
        padding: 20px;
    }
    #winter-log-viewer h1 {
        margin-top: 20px;
    }
    #winter-log-viewer .btn[disabled] {
        color: #fff;
        font-weight: bold;
        user-select: auto;
    }
    #winter-log-viewer .btn.btn-secondary[disabled] {
        color: #000;
        font-weight: normal;
    }
    #winter-log-viewer table.table tr:first-child td, #winter-log-viewer table.table tr:first-child th {
        border-top: 0;
    }
    #winter-log-viewer table.table tr td {
        font-family: monospace;
    }
    #winter-log-viewer .input-group.select-container {
        position: absolute;
        right: 0;
    }
    #winter-log-viewer .input-group.select-container .select2-container--default {
        width: auto;
    }
    #winter-log-viewer .input-group.select-container .select2-container--default .select2-selection {
        padding-right: 30px;
    }
    #winter-log-viewer .exception-list {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    #winter-log-viewer .exception-list.reverse {
        flex-direction: column-reverse;
    }
    #winter-log-viewer .exception-list .exception {
        width: 100%;
    }
    #winter-log-viewer .btn-group:not(:last-of-type) {
        margin-right: 5px;
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
    div.snippet-preview span.control { color: #7109e1; }
    div.snippet-preview span.string { color: #6a8d00; }
    div.snippet-preview span.number { color: #006ac0; }
    div.snippet-preview span.html { color: #cba604; }
    div.snippet-preview span.bool { color: #e1095c; }
    div.snippet-preview span.comment { color: #8c8c8c; }
    .trace-title {
        margin: 15px auto;
        display: block;
        font-size: 1.2em;
        font-weight: bold;
    }
    .trace-title small {
        font-size: 0.85em;
        font-weight: normal;
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
        word-break: break-word;
    }
    .trace-frame .label .item {
        font-weight: bold;
        font-style: italic;
    }
    .trace-frame .label  .app-icon{
        background: #73b2d0;
        color: #e9f3fa;
        border-radius: 6px;
        font-size: 0.8em;
        padding: 3px;
        font-weight: bold;
        float: right;
        margin-top: -2px;
    }
    .trace-frame .folded {
        display: none;
    }
    /* The following are fixes for the TailwindUI plugin */
    #winter-log-viewer hr {
        margin-bottom: 20px;
        margin-top: 20px;
    }
    #winter-log-viewer h1 {
        font-size: 36px;
    }
</style>
<div id="winter-log-viewer">
    <div class="formatted">
        <div>
            <?php if (strtolower($value['environment']['context']) === 'web'): ?>
                <table class="table table-responsive">
                    <tbody>
                        <tr>
                            <th>HTTP Method</th>
                            <td><?= $value['environment']['method'] ?></td>
                        </tr>
                        <tr>
                            <th>Url</th>
                            <td><?= $value['environment']['url'] ?></td>
                        </tr>
                        <tr>
                            <th>User Agent</th>
                            <td><?= $value['environment']['userAgent'] ?></td>
                        </tr>
                        <tr>
                            <th>Client IP</th>
                            <td><?= $value['environment']['ip'] ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>

            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" disabled class="btn btn-sm btn-secondary">Context</button>
                <button type="button" disabled class="btn btn-sm btn-primary"><?= $value['environment']['context'] ?></button>
            </div>
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" disabled class="btn btn-sm btn-secondary">Environment</button>
                <button type="button" disabled class="btn btn-sm btn-primary"><?= $value['environment']['env'] ?></button>
            </div>
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" disabled class="btn btn-sm btn-secondary">Testing</button>
                <button type="button" disabled class="btn btn-sm btn-primary"><?= $value['environment']['testing'] ? 'true' : 'false' ?></button>
            </div>

            <hr>

            <?php if ($value['exception']['previous']): ?>
                <div class="select-container input-group mb-3">
                    <select class="custom-select" id="exception-sort-order">
                        <option selected value="old">Oldest first</option>
                        <option value="new">Newest first</option>
                    </select>
                </div>
            <?php endif; ?>
        </div>
        <div class="exception-list">
            <?php foreach (getOrderedExceptionList($value['exception']) as $index => $exception): ?>
                <div class="exception">
                    <h1><?= $exception['type'] ?></h1>
                    <p class="message-log"><?= $exception['message'] ?></p>

                    <div>
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button type="button" disabled class="btn btn-sm btn-secondary">Exception</button>
                            <button type="button" disabled class="btn btn-sm btn-primary">#<?= $index ?></button>
                        </div>
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button type="button" disabled class="btn btn-sm btn-secondary">Code</button>
                            <button type="button" disabled class="btn btn-sm btn-primary"><?= $exception['code'] ?></button>
                        </div>
                    </div>

                    <div class="trace">
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
                        <span class="trace-title">Stack Trace <small>(<?= count($exception['trace']) ?> frames)</small></span>
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
    </div>
    <div class="raw" style="display: none">
        <pre class="beautifier-raw-content"><?= $value['exception']['stringTrace'] ?></pre>
    </div>
</div>

<script>
    const EDITORS = {
        vscode: { scheme: 'vscode://file/%file:%line', name: 'VS Code (vscode://)' },
        phpstorm: { scheme: 'phpstorm://open?file=%file&line=%line', name: 'PhpStorm (phpstorm://)' },
        subl: { scheme: 'subl://open?url=file://%file&line=%line', name: 'Sublime (subl://)' },
        txmt: { scheme: 'txmt://open/?url=file://%file&line=%line', name: 'TextMate (txmt://)' },
        mvim: { scheme: 'mvim://open/?url=file://%file&line=%line', name: 'MacVim (mvim://)' },
        editor: { scheme: 'editor://open/?file=%file&line=%line', name: 'Custom (editor://)' }
    };

    const REGEX = {
        editor: /idelink:\/\/([^#]+)&([0-9]+)?/
    };

    let LINKER_POPUP_CONTENT = null;

    function openWithEditor(link) {
        const matches = link.match(REGEX.editor);

        const open = function(value) {
            const editorScheme = EDITORS[value].scheme
                .replace(/%file/, matches[1])
                .replace(/%line/, matches[2]);
            window.open(link.replace(REGEX.editor, editorScheme), '_self');
        };

        console.log(matches);

        if (matches) {
            if (sessionStorage && sessionStorage.getItem('wn-exception-beautifier-editor')) {
                open(sessionStorage.getItem('wn-exception-beautifier-editor'));
            } else {
                // Create and display the popup if not already created
                if (!LINKER_POPUP_CONTENT) {
                    const title = 'Select an Editor';
                    const description = 'Choose an editor to open the file:';
                    const openWith = 'Open with:';
                    const rememberChoice = 'Remember choice for next time';
                    const open = 'Open';
                    const cancel = 'Cancel';
                    const popup = document.createElement('div');
                    popup.innerHTML = `
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">${title}</h4>
                        </div>
                        <div class="modal-body">
                            <p>${description}</p>
                            <div class="form-group">
                                <label class="control-label">${openWith}:</label>
                                <select class="form-control" name="select-exception-link-editor"></select>
                            </div>
                            <div class="checkbox custom-checkbox">
                                <input name="checkbox" value="1" type="checkbox" id="editor-remember-choice" />
                                <label for="editor-remember-choice">${rememberChoice}</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-action="submit" data-dismiss="modal">${open}</button>
                            <button type="button" class="btn btn-default" data-dismiss="popup">${cancel}</button>
                        </div>
                    `;

                    const select = popup.querySelector('select');
                    for (let key in EDITORS) {
                        if (EDITORS.hasOwnProperty(key)) {
                            const option = document.createElement('option');
                            option.value = key;
                            option.textContent = EDITORS[key].name;
                            select.appendChild(option);
                        }
                    }

                    LINKER_POPUP_CONTENT = popup.outerHTML;
                }

                const modal = document.createElement('div');
                modal.innerHTML = LINKER_POPUP_CONTENT;
                document.body.appendChild(modal);

                const popup = modal.querySelector('.modal-body');
                const select = popup.querySelector('select');
                const submitBtn = modal.querySelector('[data-action="submit"]');
                const rememberCheckbox = modal.querySelector('#editor-remember-choice');

                submitBtn.addEventListener('click', function() {
                    if (rememberCheckbox.checked && sessionStorage) {
                        sessionStorage.setItem('wn-exception-beautifier-editor', select.value);
                    }
                    open(select.value);
                    document.body.removeChild(modal);
                });

                modal.querySelector('[data-dismiss="popup"]').addEventListener('click', function() {
                    document.body.removeChild(modal);
                });
            }
        }
    }

    function formatFilePath(path, line) {
        return `<a href="javascript:" data-href="idelink://${encodeURIComponent(rewritePath(path))}&${line}">${path}</a>`;
    }

    function rewritePath(path) {
        return path.replace(/\\/g, '/');
    }

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
            // jQuery to tie in with select2
            $("select#exception-sort-order").on('change', (e) => {
                document.querySelector('#winter-log-viewer .exception-list').classList[e.target.value === 'old' ? 'remove' : 'add']('reverse');
            });
        });
    })();
</script>
