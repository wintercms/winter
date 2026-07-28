<?php namespace System\Models;

use App;
use Str;
use Model;
use Cache;
use Less_Parser;
use Exception;
use File as FileHelper;
use Winter\Storm\Parse\Assetic\Filter\LessImportResolver;

/**
 * Mail brand settings
 *
 * @package winter\wn-system-module
 * @author Alexey Bobkov, Samuel Georges
 */
class MailBrandSetting extends Model
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
    public $settingsCode = 'system_mail_brand_settings';

    /**
     * @var mixed Settings form field defitions
     */
    public $settingsFields = 'fields.yaml';

    /**
     * @var string The key to store rendered CSS in the cache under
     */
    public $cacheKey = 'system::mailbrand.custom_css';

    const WHITE_COLOR = '#fff';
    const BODY_BG = '#f5f8fa';
    const PRIMARY_BG = '#d66829';
    const POSITIVE_BG = '#52a838';
    const NEGATIVE_BG = '#e01346';
    const HEADER_COLOR = '#bbbfc3';
    const HEADING_COLOR = '#2f3133';
    const TEXT_COLOR = '#74787e';
    const LINK_COLOR = '#2da7c7';
    const FOOTER_COLOR = '#aeaeae';
    const BORDER_COLOR = '#edeff2';
    const PROMOTION_BORDER_COLOR = '#9ba2ab';

    /**
     * Validation rules
     */
    public $rules = [
    ];

    /**
     * Initialize the seed data for this model. This only executes when the
     * model is first created or reset to default.
     * @return void
     */
    public function initSettingsData()
    {
        $config = App::make('config');

        $vars = static::getCssVars();

        foreach ($vars as $var => $default) {
            $this->{$var} = $config->get('brand.mail.'.Str::studly($var), $default);
        }
    }

    public function afterSave()
    {
        $this->resetCache();
    }

    public function resetCache()
    {
        Cache::forget(self::instance()->cacheKey);
    }

    public static function renderCss()
    {
        $cacheKey = self::instance()->cacheKey;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $customCss = self::compileCss();
            Cache::forever($cacheKey, $customCss);
        }
        catch (Exception $ex) {
            $customCss = '/* ' . e($ex->getMessage()) . ' */';
        }

        return $customCss;
    }

    protected static function getCssVars()
    {
        $vars = [
            'body_bg' => self::BODY_BG,
            'content_bg' => self::WHITE_COLOR,
            'content_inner_bg' => self::WHITE_COLOR,
            'button_text_color' => self::WHITE_COLOR,
            'button_primary_bg' => self::PRIMARY_BG,
            'button_positive_bg' => self::POSITIVE_BG,
            'button_negative_bg' => self::NEGATIVE_BG,
            'header_color' => self::HEADER_COLOR,
            'heading_color' => self::HEADING_COLOR,
            'text_color' => self::TEXT_COLOR,
            'link_color' => self::LINK_COLOR,
            'footer_color' => self::FOOTER_COLOR,
            'body_border_color' => self::BORDER_COLOR,
            'subcopy_border_color' => self::BORDER_COLOR,
            'table_border_color' => self::BORDER_COLOR,
            'panel_bg' => self::BORDER_COLOR,
            'promotion_bg' => self::WHITE_COLOR,
            'promotion_border_color' => self::PROMOTION_BORDER_COLOR,
        ];

        return $vars;
    }

    protected static function makeCssVars()
    {
        $vars = static::getCssVars();

        $result = [];

        foreach ($vars as $var => $default) {
            // panel_bg -> panel-bg
            $cssVar = str_replace('_', '-', $var);

            $result[$cssVar] = self::get($var, $default);
        }

        return $result;
    }

    public static function compileCss()
    {
        $parser = new Less_Parser(['compress' => true]);

        // Refuse every @import directive. The bundled custom.less ships no imports
        // and the only user-controlled input here is CSS variable values via
        // ModifyVars below — those values are concatenated into the LESS source
        // by Less_Parser::serializeVars() with no escaping, so a malicious value
        // like `red; @import (inline) "/etc/passwd"` would otherwise reach the
        // parser as a real @import directive. See GHSA-58fp-mcx6-7qf9.
        //
        // Note: unlike BrandSetting/EditorSetting, this model deliberately does
        // not strip_tags() its renderCss() output. User input flows in only via
        // ModifyVars (CSS variable values), not as a raw CSS string, and the
        // output is consumed by the mail rendering pipeline rather than rendered
        // inline on a backend page — so the threat model strip_tags() guards
        // against does not apply here. The @import injection vector that
        // ModifyVars opens up is closed structurally by the SetImportDirs
        // deny-all gate, not by strip_tags.
        $parser->SetImportDirs(['' => LessImportResolver::makeResolver([], null)]);

        $basePath = base_path('modules/system/models/mailbrandsetting');

        $parser->ModifyVars(static::makeCssVars());

        $parser->parse(FileHelper::get($basePath . '/custom.less'));

        return $parser->getCss();
    }
}
