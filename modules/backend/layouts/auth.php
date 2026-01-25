<!DOCTYPE html>
<html lang="<?= App::getLocale() ?>" class="no-js">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=0">
        <meta name="robots" content="noindex">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="backend-base-path" content="<?= Backend::baseUrl() ?>">
        <meta name="csrf-token" content="<?= csrf_token() ?>">
        <link rel="icon" type="image/png" href="<?= e(Backend\Models\BrandSetting::getFavicon()) ?>">
        <title><?= e(trans('backend::lang.auth.title')) ?></title>
        <link rel="asset_url" href="<?=  e(Url::asset('')); ?>">

        <?php
        $coreBuild = System\Models\Parameter::get('system::core.build', 1);
        $styles = [
            Url::asset('modules/system/assets/ui/storm.css'),
            Url::asset('modules/system/assets/ui/icons.css'),
            Backend::skinAsset('assets/css/winter.css'),
        ];
        foreach ($styles as $style) {
            $this->addCss($style, [
                'build' => 'core',
                'order' => 1,
            ]);
        }
        $scripts = [
            Backend::skinAsset('assets/js/vendor/jquery.min.js'),
            Backend::skinAsset('assets/js/vendor/jquery-migrate.min.js'),
            Url::asset('modules/system/assets/js/framework.js'),
            Url::asset('modules/system/assets/ui/storm-min.js'),
            Backend::skinAsset('assets/js/winter-min.js'),
            Url::asset('modules/backend/assets/js/auth/auth.js'),
            Url::asset('modules/system/assets/js/lang/lang.'.App::getLocale().'.js'),
        ];
        foreach ($scripts as $script) {
            $this->addJs($script, [
                'build' => 'core',
                'order' => 1,
            ]);
        }
        ?>

        <?php if (!Config::get('cms.enableBackendServiceWorkers', false)): ?>
            <script>
                "use strict";
                /* Only run on HTTPS connections
                * Block off Front-end Service Worker from running in the Backend allowing security injections, see GitHub #4384
                */
                if (location.protocol === 'https:') {
                    // Unregister all service workers before signing in to prevent cache issues, see github issue: #3707
                    navigator.serviceWorker.getRegistrations().then(
                        function (registrations) {
                            registrations.forEach(function (registration) {
                                registration.unregister();
                            });
                        }
                    );
                }
            </script>
        <?php endif; ?>

        <?= $this->makeAssets() ?>
        <?= Block::placeholder('head') ?>
        <?= $this->makeLayoutPartial('custom_styles') ?>
        <?= $this->fireViewEvent('backend.layout.extendHead', ['layout' => 'auth']) ?>
    </head>
    <body class="outer <?= $this->bodyClass ?>">
        <div id="layout-canvas">
            <div class="layout">

                <div class="layout-row min-size layout-head">
                    <div class="layout-cell">
                        <h1 class="wn-logo"><?= e(Backend\Models\BrandSetting::get('app_name')) ?></h1>
                    </div>
                </div>
                <div class="layout-row">
                    <div class="layout-cell">
                        <div class="outer-form-container">
                            <?= Block::placeholder('body') ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Flash Messages -->
        <div id="layout-flash-messages"><?= $this->makeLayoutPartial('flash_messages') ?></div>

    </body>
</html>
