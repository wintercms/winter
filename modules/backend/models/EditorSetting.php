<?php

namespace Backend\Models;

use Exception;
use Illuminate\Support\Facades\Cache;
use Less_Parser;
use Winter\Storm\Database\Model;
use Winter\Storm\Parse\Assetic\Filter\LessImportResolver;
use Winter\Storm\Support\Facades\File;

/**
 * Editor settings that affect all users
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */
class EditorSetting extends Model
{
    use \System\Traits\ViewMaker;
    use \Winter\Storm\Database\Traits\Validation;

    /**
     * @var array Behaviors implemented by this model.
     */
    public $implement = [
        \System\Behaviors\SettingsModel::class
    ];

    /**
     * @var string Unique code
     */
    public $settingsCode = 'backend_editor_settings';

    /**
     * @var mixed Settings form field definitions
     */
    public $settingsFields = 'fields.yaml';

    /**
     * @var string The key to store rendered CSS in the cache under
     */
    public $cacheKey = 'backend::editor.custom_css';

    protected $defaultHtmlAllowEmptyTags = 'textarea, a, iframe, object, video, style, script, .fa, .fr-emoticon, .fr-inner, path, line, hr, i';

    protected $defaultHtmlAllowTags = 'a, abbr, address, area, article, aside, audio, b, bdi, bdo, blockquote, br, button, canvas, caption, cite, code, col, colgroup, datalist, dd, del, details, dfn, dialog, div, dl, dt, em, embed, fieldset, figcaption, figure, footer, form, h1, h2, h3, h4, h5, h6, header, hgroup, hr, i, iframe, img, input, ins, kbd, keygen, label, legend, li, link, main, map, mark, menu, menuitem, meter, nav, noscript, object, ol, optgroup, option, output, p, param, pre, progress, queue, rp, rt, ruby, s, samp, script, style, section, select, small, source, span, strike, strong, sub, summary, sup, table, tbody, td, textarea, tfoot, th, thead, time, title, tr, track, u, ul, var, video, wbr';

    protected $defaultHtmlAllowAttributes = 'accept, accept-charset, accesskey, action, align, allowfullscreen, allowtransparency, alt, aria-.*, async, autocomplete, autofocus, autoplay, autosave, background, bgcolor, border, charset, cellpadding, cellspacing, checked, cite, class, color, cols, colspan, content, contenteditable, contextmenu, controls, coords, data, data-.*, datetime, default, defer, dir, dirname, disabled, download, draggable, dropzone, enctype, for, form, formaction, frameborder, headers, height, hidden, high, href, hreflang, http-equiv, icon, id, ismap, itemprop, keytype, kind, label, lang, language, list, loop, low, max, maxlength, media, method, min, mozallowfullscreen, multiple, muted, name, novalidate, open, optimum, pattern, ping, placeholder, playsinline, poster, preload, pubdate, radiogroup, readonly, rel, required, reversed, rows, rowspan, sandbox, scope, scoped, scrolling, seamless, selected, shape, size, sizes, span, src, srcdoc, srclang, srcset, start, step, summary, spellcheck, style, tabindex, target, title, type, translate, usemap, value, valign, webkitallowfullscreen, width, wrap';

    protected $defaultHtmlNoWrapTags = 'figure, script, style';

    protected $defaultHtmlRemoveTags = 'script, style, base';

    protected $defaultHtmlLineBreakerTags = 'figure, table, hr, iframe, form, dl';

    protected $defaultHtmlStyleImage = [
        'oc-img-rounded' => 'Rounded',
        'oc-img-bordered' => 'Bordered',
    ];

    protected $defaultHtmlStyleLink = [
        'oc-link-green' => 'Green',
        'oc-link-strong' => 'Strong',
    ];

    protected $defaultHtmlStyleParagraph = [
        'oc-text-bordered' => 'Bordered',
        'oc-text-gray' => 'Gray',
        'oc-text-spaced' => 'Spaced',
        'oc-text-uppercase' => 'Uppercase',
    ];

    protected $defaultHtmlStyleTable = [
        'oc-dashed-borders' => 'Dashed Borders',
        'oc-alternate-rows' => 'Alternate Rows',
    ];

    protected $defaultHtmlStyleTableCell = [
        'oc-cell-highlighted' => 'Highlighted',
        'oc-cell-thick-border' => 'Thick Border',
    ];

    protected $defaultHtmlParagraphFormats = [
        'N' => 'Normal',
        'H1' => 'Heading 1',
        'H2' => 'Heading 2',
        'H3' => 'Heading 3',
        'H4' => 'Heading 4',
        'PRE' => 'Code',
    ];

    /**
     * Editor toolbar presets for Froala.
     */
    protected $editorToolbarPresets = [
        'default' => 'paragraphFormat, paragraphStyle, quote, bold, italic, align, formatOL, formatUL, insertTable,
                      insertLink, insertImage, insertVideo, insertAudio, insertFile, insertHR, html',
        'minimal' => 'paragraphFormat, bold, italic, underline, |, insertLink, insertImage, |, html',
        'full'    => 'undo, redo, |, bold, italic, underline, |, paragraphFormat, paragraphStyle, inlineStyle, |,
                      strikeThrough, subscript, superscript, clearFormatting, |, fontFamily, fontSize, |, color,
                      emoticons, -, selectAll, |, align, formatOL, formatUL, outdent, indent, quote, |, insertHR,
                      insertLink, insertImage, insertVideo, insertAudio, insertFile, insertTable, |, selectAll,
                      html, fullscreen',
    ];

    /**
     * Validation rules
     */
    public $rules = [];

    /**
     * Initialize the seed data for this model. This only executes when the
     * model is first created or reset to default.
     * @return void
     */
    public function initSettingsData()
    {
        $this->html_allow_empty_tags = $this->defaultHtmlAllowEmptyTags;
        $this->html_allow_tags = $this->defaultHtmlAllowTags;
        $this->html_allow_attributes = $this->defaultHtmlAllowAttributes;
        $this->html_no_wrap_tags = $this->defaultHtmlNoWrapTags;
        $this->html_remove_tags = $this->defaultHtmlRemoveTags;
        $this->html_line_breaker_tags = $this->defaultHtmlLineBreakerTags;
        $this->html_custom_styles = File::get(base_path().'/modules/backend/models/editorsetting/default_styles.less');
        $this->html_style_image = $this->makeStylesForTable($this->defaultHtmlStyleImage);
        $this->html_style_link = $this->makeStylesForTable($this->defaultHtmlStyleLink);
        $this->html_style_paragraph = $this->makeStylesForTable($this->defaultHtmlStyleParagraph);
        $this->html_style_table = $this->makeStylesForTable($this->defaultHtmlStyleTable);
        $this->html_style_table_cell = $this->makeStylesForTable($this->defaultHtmlStyleTableCell);
        $this->html_paragraph_formats = $this->makeFormatsForTable($this->defaultHtmlParagraphFormats);
    }

    public function afterFetch()
    {
        if (!isset($this->value['html_paragraph_formats'])) {
            $this->html_paragraph_formats = $this->makeFormatsForTable($this->defaultHtmlParagraphFormats);
            $this->save();
        }
    }

    public function afterSave()
    {
        Cache::forget(self::instance()->cacheKey);
    }

    protected function makeStylesForTable($arr)
    {
        $count = 0;

        return array_build($arr, function ($key, $value) use (&$count) {
            return [$count++, ['class_label' => $value, 'class_name' => $key]];
        });
    }

    protected function makeFormatsForTable($arr)
    {
        $count = 0;

        return array_build($arr, function ($key, $value) use (&$count) {
            return [$count++, ['format_label' => $value, 'format_tag' => $key]];
        });
    }

    /**
     * Same as getConfigured but uses a special structure for styles.
     *
     * The RichEditor hands these straight to the editor, which concatenates both halves into
     * its dropdown markup with no encoding of its own:
     *
     *     <a class="fr-command CLASS" data-param1="CLASS" title="LABEL">LABEL</a>
     *
     * so both are HTML encoded here, at the point the editor reads them. Encoding rather than
     * constraining keeps every class name that works today - a Tailwind utility spells
     * `md:text-lg`, `w-1/2` or `[&>p]:mt-4` - because the editor reads the attribute back
     * through the DOM, which decodes it again. Doing it on read rather than on save also
     * covers values that were already stored.
     *
     * @return mixed
     */
    public static function getConfiguredStyles($key, $default = null)
    {
        return static::getConfiguredArray($key, $default, function ($key, $value) {
            if (array_has($value, ['class_name', 'class_label'])) {
                $className = static::renderableEditorValue(array_get($value, 'class_name'));
                $classLabel = static::renderableEditorValue(array_get($value, 'class_label'));

                if ($className === null || $classLabel === null) {
                    // Left out by the array_filter() in getConfiguredArray()
                    return ['', null];
                }

                return [e($className), e($classLabel)];
            }
        });
    }

    /**
     * Same as getConfigured but uses a special structure for paragraph formats.
     *
     * The editor uses the tag as a literal element name and the label as both an attribute
     * value and element content in its dropdown markup, neither encoded:
     *
     *     <TAG style="..."><a data-param1="TAG" title="LABEL">LABEL</a></TAG>
     *
     * The tag is emitted as an element name rather than as text, so it has to be a bare name;
     * one that is not is left out rather than rewritten. The label is encoded.
     *
     * @return mixed
     */
    public static function getConfiguredFormats($key, $default = null)
    {
        return static::getConfiguredArray($key, $default, function ($key, $value) {
            if (array_has($value, ['format_tag', 'format_label'])) {
                $formatTag = static::renderableEditorValue(array_get($value, 'format_tag'));
                $formatLabel = static::renderableEditorValue(array_get($value, 'format_label'));

                // Trimmed because this is an element name, where surrounding whitespace can
                // never be meaningful, and leaving the row out over it would silently drop a
                // format that works today. A class name is left exactly as written instead,
                // since there two names differing only by whitespace are two names.
                if ($formatTag !== null) {
                    $formatTag = trim($formatTag);
                }

                if ($formatTag === null || $formatLabel === null || !static::isValidFormatTag($formatTag)) {
                    // Left out by the array_filter() in getConfiguredArray()
                    return ['', null];
                }

                return [$formatTag, e($formatLabel)];
            }
        });
    }

    /**
     * Returns the given configured value as a string, or null if it is not one the editor can
     * be handed at all.
     *
     * A scalar and an object that stringifies both qualify - a translated label arrives as
     * either - while an array or a plain object does not.
     *
     * @param mixed $value
     */
    protected static function renderableEditorValue($value): ?string
    {
        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        return null;
    }

    /**
     * Is this paragraph format tag safe to hand to the editor as it was written?
     *
     * The tag is emitted as a literal element name, so it has to be a bare element name: a
     * letter followed by letters, digits, hyphens or underscores. The hyphen is what a custom
     * element name is required to contain, and `N` is the editor's own sentinel for the
     * default tag.
     */
    protected static function isValidFormatTag(string $formatTag): bool
    {
        return (bool) preg_match('/^[A-Za-z][A-Za-z0-9_-]*$/D', $formatTag);
    }

    protected static function getConfiguredArray($key, $default = null, $callback = null)
    {
        $instance = static::instance();

        $value = $instance->get($key);

        $defaultValue = $instance->getDefaultValue($key);

        if (is_array($value) && is_callable($callback)) {
            $value = array_filter(array_build($value, $callback));
        }

        return $value != $defaultValue ? $value : $default;
    }

    /**
     * Returns the value only if it differs from the default value.
     * @return mixed
     */
    public static function getConfigured($key, $default = null)
    {
        $instance = static::instance();

        $value = $instance->get($key);

        $defaultValue = $instance->getDefaultValue($key);

        return $value != $defaultValue ? $value : $default;
    }

    public function getDefaultValue($attribute)
    {
        $property = 'default'.studly_case($attribute);

        return $this->$property;
    }

    /**
     * Return the editor toolbar presets without line breaks.
     * @return array
     */
    public function getEditorToolbarPresets()
    {
        return array_map(function ($value) {
            return preg_replace('/\s+/', ' ', $value);
        }, $this->editorToolbarPresets);
    }

    public static function renderCss()
    {
        $cacheKey = self::instance()->cacheKey;
        if (Cache::has($cacheKey)) {
            return strip_tags(Cache::get($cacheKey));
        }

        try {
            $customCss = self::compileCss();
            Cache::forever($cacheKey, $customCss);
        } catch (Exception $ex) {
            $customCss = '/* ' . e($ex->getMessage()) . ' */';
        }

        return strip_tags($customCss);
    }

    public static function compileCss()
    {
        $parser = new Less_Parser(['compress' => true]);

        // Refuse every @import directive. There is no bundled .less file to
        // import here, and the admin-supplied html_custom_styles field has no
        // legitimate use for @import. Without this gate, an @import (inline)
        // directive in user CSS would disclose server files via the
        // wikimedia/less.php raw-path fallback. See GHSA-58fp-mcx6-7qf9.
        $parser->SetImportDirs(['' => LessImportResolver::makeResolver([], null)]);

        $customStyles = '.fr-view {';
        $customStyles .= self::get('html_custom_styles');
        $customStyles .= '}';

        $parser->parse($customStyles);

        return $parser->getCss();
    }
}
