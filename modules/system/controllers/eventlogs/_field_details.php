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
        'control' => '/\b(for|foreach|while|class |extends|yield from|yield|echo|fn|implements|try|catch|finally|throw|new|instanceof| parent|final|function|return|unset|static|public|protected|private|count|global|if|else|else if|intval|int|array)\b/',
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
function makeSnippet(array $snippet, string $file, ?int $highlight = null): string
{
    return implode(
        "\n",
        array_reduce(
            array_keys($snippet),
            function (array $carry, $key) use ($snippet, $file, $highlight) {
                $carry[] = sprintf(
                    '<div class="preview-line%s"><span class="line-number" data-idelink="idelink://%s&%3$d""><span class="icon wn-icon-file-pen"></span>%3$d</span>: %4$s</div>',
                    ($key + 1 === $highlight ? ' highlight' : ''),
                    urlencode(str_replace('\\', '/', $file)),
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
    div.snippet-preview div.preview-line span.line-number {
        cursor: pointer;
        position: relative;
    }
    div.snippet-preview div.preview-line span.line-number .icon {
        opacity: 0;
        position: absolute;
        left: calc(100% + 1em);
        transition: opacity linear .2s;
    }
    div.snippet-preview div.preview-line:hover span.line-number .icon {
        opacity: 1;
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
                            <th><?= e(trans('system::lang.event_log.details.http_method')) ?></th>
                            <td><?= e($value['environment']['method']) ?></td>
                        </tr>
                        <tr>
                            <th><?= e(trans('system::lang.event_log.details.url')) ?></th>
                            <td>
                                <a href="<?= e($value['environment']['url']) ?>" target="_blank" rel="noopener">
                                    <span class="wn-icon-link"></span><?= e($value['environment']['url']) ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th><?= e(trans('system::lang.event_log.details.user_agent')) ?></th>
                            <td><?= e($value['environment']['userAgent']) ?></td>
                        </tr>
                        <tr>
                            <th><?= e(trans('system::lang.event_log.details.client_ip')) ?></th>
                            <td><?= e($value['environment']['ip']) ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>

            <div class="btn-group" role="group" title="<?= e(trans('system::lang.event_log.details.exception_context')) ?>">
                <button type="button" disabled class="btn btn-sm btn-secondary"><?= e(trans('system::lang.event_log.details.context')) ?></button>
                <button type="button" disabled class="btn btn-sm btn-primary"><?= e($value['environment']['context']) ?></button>
            </div>
            <div class="btn-group" role="group" title="<?= e(trans('system::lang.event_log.details.exception_app_env')) ?>">
                <button type="button" disabled class="btn btn-sm btn-secondary"><?= e(trans('system::lang.event_log.details.environment')) ?></button>
                <button type="button" disabled class="btn btn-sm btn-primary"><?= e($value['environment']['env']) ?></button>
            </div>
            <?php if (strtolower($value['environment']['context']) === 'web'): ?>
                <div class="btn-group" role="group" title="<?= e(trans('system::lang.event_log.details.exception_encountered_backend')) ?>">
                    <button type="button" disabled class="btn btn-sm btn-secondary"><?= e(trans('system::lang.event_log.details.backend')) ?></button>
                    <button type="button" disabled class="btn btn-sm btn-primary"><?= $value['environment']['backend'] ? 'true' : 'false' ?></button>
                </div>
            <?php endif; ?>
            <div class="btn-group" role="group" title="<?= e(trans('system::lang.event_log.details.exception_encountered_unit_test')) ?>">
                <button type="button" disabled class="btn btn-sm btn-secondary"><?= e(trans('system::lang.event_log.details.testing')) ?></button>
                <button type="button" disabled class="btn btn-sm btn-primary"><?= $value['environment']['testing'] ? 'true' : 'false' ?></button>
            </div>

            <hr>

            <?php if ($value['exception']['previous']): ?>
                <div class="select-container input-group mb-3">
                    <select class="custom-select" id="exception-sort-order">
                        <option selected value="old"><?= e(trans('system::lang.event_log.details.oldest_first')) ?></option>
                        <option value="new"><?= e(trans('system::lang.event_log.details.newest_first')) ?></option>
                    </select>
                </div>
            <?php endif; ?>
        </div>
        <div class="exception-list">
            <?php foreach (getOrderedExceptionList($value['exception']) as $index => $exception): ?>
                <div class="exception">
                    <h1><?= e($exception['type']) ?></h1>
                    <p class="message-log"><?= e($exception['message']) ?></p>

                    <div>
                        <div class="btn-group" role="group" title="<?= e(trans('system::lang.event_log.details.exception_index')) ?>">
                            <button type="button" disabled class="btn btn-sm btn-secondary"><?= e(trans('system::lang.event_log.details.exception')) ?></button>
                            <button type="button" disabled class="btn btn-sm btn-primary">#<?= e($index) ?></button>
                        </div>
                        <div class="btn-group" role="group" title="<?= e(trans('system::lang.event_log.details.exception_code')) ?>">
                            <button type="button" disabled class="btn btn-sm btn-secondary"><?= e(trans('system::lang.event_log.details.code')) ?></button>
                            <button type="button" disabled class="btn btn-sm btn-primary"><?= e($exception['code']) ?></button>
                        </div>
                    </div>

                    <div class="trace">
                        <div class="trace-frame">
                            <div class="label">
                                <span class="item"><?= e($exception['file']) ?></span>
                                at line <span class="item"><?= e($exception['line']) ?></span>
                            </div>
                            <?php if ($exception['snippet']): ?>
                                <div class="snippet-preview-container">
                                    <div class="snippet-preview">
                                        <?= makeSnippet($exception['snippet'], $exception['file'], $exception['line']) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <span class="trace-title"><?= trans('system::lang.event_log.details.stack_trace', ['count' => e(count($exception['trace']))]) ?></span>
                        <div class="trace">
                            <?php foreach ($exception['trace'] as $traceIndex => $frame): ?>
                                <div class="trace-frame">
                                    <div class="label">
                                        <span class="item">#<?= e($traceIndex) ?> <?= e($frame['file']) ?></span>
                                        in <span class="item"><?= $frame['class'] && !str_contains($frame['function'], '{') ? e($frame['class']) . '::' : '' ?><?= e($frame['function']) ?></span>
                                        <?php if ($frame['line']): ?>
                                            at line <span class="item"><?= e($frame['line']) ?></span>
                                        <?php endif; ?>
                                        <?php if ($frame['arguments']): ?>
                                            with argument<?= count($frame['arguments']) > 1 ? 's' : '' ?>: (<span class="item"><?= implode('</span>, <span class="item">', array_map('e', $frame['arguments'])) ?></span>)
                                        <?php endif; ?>
                                        <?php if ($frame['in_app']): ?>
                                            <span class="app-icon"><?= e(trans('system::lang.event_log.details.in_app')) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($frame['snippet']): ?>
                                        <div class="snippet-preview-container <?= $frame['in_app'] ? 'unfolded' : 'folded' ?>">
                                            <div class="snippet-preview">
                                                <?= makeSnippet($frame['snippet'], $frame['file'], $frame['line']) ?>
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
        <pre class="beautifier-raw-content"><?= e($value['exception']['stringTrace']) ?></pre>
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
            // jQuery to tie in with select2
            $("select#exception-sort-order").on('change', (e) => {
                document.querySelector('#winter-log-viewer .exception-list').classList[e.target.value === 'old' ? 'remove' : 'add']('reverse');
            });

            // Luke made me do it
            // Script to load files in editors
            (() => {
                const editors = {
                    vscode: { scheme: 'vscode://file/%file:%line', name: 'VS Code (vscode://)' },
                    phpstorm: { scheme: 'phpstorm://open?file=%file&line=%line', name: 'PhpStorm (phpstorm://)' },
                    subl: { scheme: 'subl://open?url=file://%file&line=%line', name: 'Sublime (subl://)' },
                    txmt: { scheme: 'txmt://open/?url=file://%file&line=%line', name: 'TextMate (txmt://)' },
                    mvim: { scheme: 'mvim://open/?url=file://%file&line=%line', name: 'MacVim (mvim://)' },
                    editor: { scheme: 'editor://open/?file=%file&line=%line', name: 'Custom (editor://)' }
                };

                const ideLinkRegex = /idelink:\/\/([^#]+)&([0-9]+)?/;

                function openWithEditor(link) {
                    const matches = link.match(ideLinkRegex);

                    const open = function(value) {
                        const editorScheme = editors[value].scheme
                            .replace(/%file/, matches[1])
                            .replace(/%line/, matches[2]);
                        window.open(link.replace(ideLinkRegex, editorScheme), '_self');
                    };

                    if (!matches) {
                        return;
                    }

                    if (sessionStorage && sessionStorage.getItem('wn-exception-beautifier-editor')) {
                        open(sessionStorage.getItem('wn-exception-beautifier-editor'));
                        return;
                    }

                    const title = 'Select an Editor';
                    const description = 'Choose an editor to open the file:';
                    const openWith = 'Open with:';
                    const rememberChoice = 'Remember choice for next time';
                    const openString = 'Open';
                    const cancel = 'Cancel';

                    $.popup({
                        size: 'large idelink-popup',
                        content: `
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
                                <button type="button" class="btn btn-primary" data-action="submit" data-dismiss="modal">${openString}</button>
                                <button type="button" class="btn btn-default" data-dismiss="popup">${cancel}</button>
                            </div>
                        `,
                    });

                    const popup = document.querySelector('.idelink-popup');
                    const select = popup.querySelector('select');

                    Object.entries(editors).forEach(([name, editor]) => {
                        const option = document.createElement('option');
                        option.value = name;
                        option.textContent = editor.name;
                        select.appendChild(option);
                    });

                    const submitBtn = popup.querySelector('[data-action="submit"]');
                    const closeBtn = popup.querySelector('[data-dismiss="popup"]');
                    const rememberCheckbox = popup.querySelector('#editor-remember-choice');

                    submitBtn.addEventListener('click', function() {
                        if (rememberCheckbox.checked && sessionStorage) {
                            sessionStorage.setItem('wn-exception-beautifier-editor', select.value);
                        }
                        open(select.value);
                        closeBtn.click();
                        popup.remove();
                    });
                }

                document.querySelectorAll('div.snippet-preview div.preview-line span.line-number[data-idelink]').forEach((lineNumber) => {
                    lineNumber.addEventListener('click', () => {
                        openWithEditor(lineNumber.dataset.idelink);
                    })
                });
            })();
        });
    })();
</script>
