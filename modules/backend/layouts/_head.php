<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=0, minimal-ui">
<meta name="robots" content="noindex">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="app-timezone" content="<?= e(Config::get('app.timezone')) ?>">
<meta name="backend-base-path" content="<?= Backend::baseUrl() ?>">
<meta name="backend-timezone" content="<?= e(Backend\Models\Preference::get('timezone')) ?>">
<meta name="backend-locale" content="<?= e(Backend\Models\Preference::get('locale')) ?>">
<meta name="csrf-token" content="<?= csrf_token() ?>">
<link rel="icon" type="image/png" href="<?= e(Backend\Models\BrandSetting::getFavicon()) ?>">
<title data-title-template="<?= empty($this->pageTitleTemplate) ? '%s' : e($this->pageTitleTemplate) ?> | <?= e(Backend\Models\BrandSetting::get('app_name')) ?>">
    <?= e(trans($this->pageTitle)) ?> | <?= e(Backend\Models\BrandSetting::get('app_name')) ?>
</title>

<?= $this->makeAssets() ?>

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

<?= Block::placeholder('head') ?>
<?= $this->makeLayoutPartial('custom_styles') ?>
