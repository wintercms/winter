<?php namespace System\Classes;

use App;
use Markdown;
use System\Models\MailLayout;
use System\Models\MailPartial;
use System\Models\MailTemplate;
use System\Models\MailBrandSetting;
use System\Helpers\View as ViewHelper;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * This class manages Mail sending functions
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class MailManager
{
    use \Winter\Storm\Support\Traits\Singleton;

    /**
     * @var array Cache of registration callbacks.
     */
    protected $callbacks = [];

    /**
     * @var array A cache of customised mail templates.
     */
    protected $templateCache = [];

    /**
     * @var array List of registered templates in the system
     */
    protected $registeredTemplates;

    /**
     * @var array List of registered partials in the system
     */
    protected $registeredPartials;

    /**
     * @var array List of registered layouts in the system
     */
    protected $registeredLayouts;

    /**
     * @var bool Internal marker for rendering mode
     */
    protected $isHtmlRenderMode = false;

    /**
     * Same as `addContentToMailer` except with raw content.
     *
     * @return bool
     */
    public function addRawContentToMailer($message, $content, $data)
    {
        $template = new MailTemplate;

        $template->fillFromContent($content);

        $this->addContentToMailerInternal($message, $template, $data);

        return true;
    }

    /**
     * This function hijacks the `addContent` method of the `Winter\Storm\Mail\Mailer`
     * class, using the `mailer.beforeAddContent` event.
     *
     * @param \Illuminate\Mail\Message $message
     * @param string $code
     * @param array $data
     * @param bool $plainOnly Add only plain text content to the message
     * @return bool
     */
    public function addContentToMailer($message, $code, $data, $plainOnly = false)
    {
        // We only handle mail template names as a string, let the caller handle the content if we receive anything else
        if (!is_string($code)) {
            return false;
        }

        if (isset($this->templateCache[$code])) {
            $template = $this->templateCache[$code];
        }
        else {
            $this->templateCache[$code] = $template = MailTemplate::findOrMakeTemplate($code);
        }

        if (!$template) {
            return false;
        }

        $this->addContentToMailerInternal($message, $template, $data, $plainOnly);

        return true;
    }

    /**
     * Internal method used to share logic between `addRawContentToMailer` and `addContentToMailer`
     *
     * @param \Illuminate\Mail\Message $message
     * @param string $template
     * @param array $data
     * @param bool $plainOnly Add only plain text content to the message
     * @return void
     */
    protected function addContentToMailerInternal($message, $template, $data, $plainOnly = false)
    {
        /*
         * Inject global view variables
         */
        $globalVars = ViewHelper::getGlobalVars();
        if (!empty($globalVars)) {
            $data = (array) $data + $globalVars;
        }

        /*
         * Subject
         */
        $symfonyMessage = $message->getSymfonyMessage();

        if (empty($symfonyMessage->getSubject())) {
            $message->subject($this->renderTwig($template->subject, $data, $this->isUserEdited($template)));
        }

        $data += [
            'subject' => $symfonyMessage->getSubject()
        ];

        if (!$plainOnly) {
            /*
             * HTML contents
             */
            $html = $this->renderTemplate($template, $data);

            $message->html($html);
        }

        /*
         * Text contents
         */
        $text = $this->renderTextTemplate($template, $data);

        $message->text($text);
    }

    //
    // Rendering
    //

    /**
     * Render the Markdown template into HTML.
     *
     * @param  string  $content
     * @param  array  $data
     * @param  bool  $sandboxed Render in the sandboxed mailer environment, for content a backend
     * user wrote rather than content that came from a view file.
     * @return string
     */
    public function render($content, $data = [], $sandboxed = false)
    {
        if (!$content) {
            return '';
        }

        $html = $this->renderTwig($content, $data, $sandboxed);

        $html = Markdown::parseSafe($html);

        return $html;
    }

    public function renderTemplate($template, $data = [])
    {
        $this->isHtmlRenderMode = true;

        $html = $this->render($template->content_html, $data, $this->isUserEdited($template));

        $css = MailBrandSetting::renderCss();

        $disableAutoInlineCss = false;

        if ($template->layout) {
            $disableAutoInlineCss = array_get($template->layout->options, 'disable_auto_inline_css', $disableAutoInlineCss);

            // The layout is judged on its own origin, not the template's. The content it is handed
            // has already finished rendering and arrives as a string, so a user-edited template
            // inside a layout that came from a view file stays contained.
            $html = $this->renderTwig($template->layout->content_html, [
                'content' => $html,
                'css' => $template->layout->content_css,
                'brandCss' => $css,
            ] + (array) $data, $this->isUserEdited($template->layout));

            $css .= PHP_EOL . $template->layout->content_css;
        }

        if (!$disableAutoInlineCss) {
            $html = (new CssToInlineStyles)->convert($html, $css);
        }

        return $html;
    }

    /**
     * Render the Markdown template into text.
     * @param $content
     * @param array $data
     * @param bool $sandboxed Render in the sandboxed mailer environment, for content a backend
     * user wrote rather than content that came from a view file.
     * @return string
     */
    public function renderText($content, $data = [], $sandboxed = false)
    {
        if (!$content) {
            return '';
        }

        $text = $this->renderTwig($content, $data, $sandboxed);

        $text = html_entity_decode(preg_replace("/[\r\n]{2,}/", "\n\n", $text), ENT_QUOTES, 'UTF-8');

        return $text;
    }

    public function renderTextTemplate($template, $data = [])
    {
        $this->isHtmlRenderMode = false;

        $templateText = $template->content_text;

        if (!strlen($template->content_text)) {
            $templateText = $template->content_html;
        }

        $text = $this->renderText($templateText, $data, $this->isUserEdited($template));

        if ($template->layout) {
            // Per record, as in renderTemplate() above.
            $text = $this->renderTwig($template->layout->content_text, [
                'content' => $text
            ] + (array) $data, $this->isUserEdited($template->layout));
        }

        return $text;
    }

    public function renderPartial($code, array $params = [])
    {
        if (!$partial = MailPartial::findOrMakePartial($code)) {
            return '<!-- Missing partial: '.$code.' -->';
        }

        if ($this->isHtmlRenderMode) {
            $content = $partial->content_html;
        }
        else {
            $content = $partial->content_text ?: $partial->content_html;
        }

        if (!strlen(trim($content))) {
            return '';
        }

        return $this->renderTwig($content, $params, $this->isUserEdited($partial));
    }

    /**
     * Whether this record's content was written in the backend rather than coming from the view
     * file the record was registered from.
     *
     * For a template or a partial, `is_custom` is the authority rather than a convenience: a
     * record with it falsy has its content replaced from that view file every time it is read
     * (MailTemplate::afterFetch(), MailPartial::afterFetch()), and the backend sets it on every
     * save (MailTemplates::formBeforeSave(), MailPartials::formBeforeSave()). So falsy means the
     * content in hand is the file's, whoever the record belongs to.
     *
     * A layout has no such flag and no such refill. MailLayout::createLayouts() copies the view
     * file into a row once, and from then on the row is the only copy and the backend edits it in
     * place - so there is nothing that can show a layout's content still matches the file it came
     * from, and it is treated as written in the backend. That costs a layout the markup functions,
     * which is what a layout had before this release anyway; the alternative is a new column whose
     * backfill would have to guess the same answer for every row that already exists.
     *
     * @param \System\Models\MailTemplate|\System\Models\MailLayout|\System\Models\MailPartial $record
     */
    protected function isUserEdited($record): bool
    {
        if ($record instanceof MailLayout) {
            return true;
        }

        return (bool) $record->is_custom;
    }

    /**
     * Internal helper for rendering one piece of mail content.
     *
     * Mail content reaches here from one of two places, and they are not equally trusted. A mail
     * view is a file in a repository, written by whoever wrote the plugin, so it renders in the
     * plain mailer environment. A mail template, layout or partial stored in the database was
     * written in the backend by someone holding only `system.manage_mail_templates`, which grants
     * no file access and no ability to author PHP, so it renders in the sandboxed one.
     *
     * The choice is made per record, never inherited: see renderTemplate() for a layout, and
     * renderPartial() for a `{% partial %}`.
     */
    protected function renderTwig(string $content, array $data = [], bool $sandboxed = false): string
    {
        return App::make($sandboxed ? 'twig.environment.mailer.sandboxed' : 'twig.environment.mailer')
                ->createTemplate($content)
                ->render($data);
    }

    //
    // Registration
    //

    /**
     * Loads registered mail templates from modules and plugins
     * @return void
     */
    public function loadRegisteredTemplates()
    {
        foreach ($this->callbacks as $callback) {
            $callback($this);
        }

        $plugins = PluginManager::instance()->getPlugins();
        foreach ($plugins as $pluginId => $pluginObj) {
            $layouts = $pluginObj->registerMailLayouts();
            if (is_array($layouts)) {
                $this->registerMailLayouts($layouts);
            }

            $templates = $pluginObj->registerMailTemplates();
            if (is_array($templates)) {
                $this->registerMailTemplates($templates);
            }

            $partials = $pluginObj->registerMailPartials();
            if (is_array($partials)) {
                $this->registerMailPartials($partials);
            }
        }
    }

    /**
     * Returns a list of the registered templates.
     * @return array
     */
    public function listRegisteredTemplates()
    {
        if ($this->registeredTemplates === null) {
            $this->loadRegisteredTemplates();
        }

        return $this->registeredTemplates;
    }

    /**
     * Returns a list of the registered partials.
     * @return array
     */
    public function listRegisteredPartials()
    {
        if ($this->registeredPartials === null) {
            $this->loadRegisteredTemplates();
        }

        return $this->registeredPartials;
    }

    /**
     * Returns a list of the registered layouts.
     * @return array
     */
    public function listRegisteredLayouts()
    {
        if ($this->registeredLayouts === null) {
            $this->loadRegisteredTemplates();
        }

        return $this->registeredLayouts;
    }

    /**
     * Registers a callback function that defines mail templates.
     * The callback function should register templates by calling the manager's
     * registerMailTemplates() function. This instance is passed to the
     * callback function as an argument. Usage:
     *
     *     MailManager::registerCallback(function ($manager) {
     *         $manager->registerMailTemplates([...]);
     *     });
     *
     * @param callable $callback A callable function.
     */
    public function registerCallback(callable $callback)
    {
        $this->callbacks[] = $callback;
    }

    /**
     * Registers mail views and manageable templates.
     */
    public function registerMailTemplates(array $definitions)
    {
        if (!$this->registeredTemplates) {
            $this->registeredTemplates = [];
        }

        // Prior sytax where (key) code => (value) description
        if (!isset($definitions[0])) {
            $definitions = array_keys($definitions);
        }

        $definitions = array_combine($definitions, $definitions);

        $this->registeredTemplates = $definitions + $this->registeredTemplates;
    }

    /**
     * Registers mail views and manageable layouts.
     */
    public function registerMailPartials(array $definitions)
    {
        if (!$this->registeredPartials) {
            $this->registeredPartials = [];
        }

        $this->registeredPartials = $definitions + $this->registeredPartials;
    }

    /**
     * Registers mail views and manageable layouts.
     */
    public function registerMailLayouts(array $definitions)
    {
        if (!$this->registeredLayouts) {
            $this->registeredLayouts = [];
        }

        $this->registeredLayouts = $definitions + $this->registeredLayouts;
    }
}
