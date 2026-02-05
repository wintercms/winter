<?php

namespace System\Controllers\Updates\Traits;

use Cms\Classes\ThemeManager;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use October\Rain\Exception\ApplicationException;
use System\Classes\Core\MarketPlaceApi;
use System\Classes\UpdateManager;
use System\Models\Parameter;
use Winter\Storm\Support\Facades\Flash;

trait ManagesThemes
{
    protected function getInstalledThemes(): array
    {
        $history = Parameter::get('system::theme.history', []);
        $installed = MarketPlaceApi::instance()->requestProductDetails(array_keys($history), 'theme');

        /*
         * Splice in the directory names
         */
        foreach ($installed as $key => $data) {
            $code = array_get($data, 'code');
            $installed[$key]['dirName'] = array_get($history, $code, $code);
        }

        return $installed;
    }

    public function onGetPopularThemes(): array
    {
        return [
            'result' => $this->filterPopularProducts(
                MarketPlaceApi::instance()->requestPopularProducts('theme'),
                $this->getInstalledThemes()
            )
        ];
    }

    /**
     * Validate the theme code and execute the theme installation
     */
    public function onInstallTheme()
    {
        try {
            if (!$code = trim(post('code'))) {
                throw new ApplicationException(Lang::get('system::lang.install.missing_theme_name'));
            }

            $result = MarketPlaceApi::instance()->request(MarketPlaceApi::REQUEST_THEME_DETAIL, $code);

            if (!isset($result['code']) || !isset($result['hash'])) {
                throw new ApplicationException(Lang::get('system::lang.server.response_invalid'));
            }

            $name = $result['code'];
            $hash = $result['hash'];
            $themes = [$name => $hash];
            $plugins = $this->appendRequiredPlugins([], $result);

            /*
             * Update steps
             */
            $updateSteps = $this->buildUpdateSteps(null, $plugins, $themes, true);

            /*
             * Finish up
             */
            $updateSteps[] = [
                'code'  => 'completeInstall',
                'label' => Lang::get('system::lang.install.install_completing'),
            ];

            $this->vars['updateSteps'] = $updateSteps;

            return $this->makePartial('execute');
        }
        catch (Exception $ex) {
            $this->handleError($ex);
            return $this->makePartial('theme_form');
        }
    }

    /**
     * Deletes a single theme from the system.
     */
    public function onRemoveTheme(): RedirectResponse
    {
        if ($themeCode = post('code')) {
            ThemeManager::instance()->deleteTheme($themeCode);

            Flash::success(trans('cms::lang.theme.delete_theme_success'));
        }

        return Redirect::refresh();
    }
}
