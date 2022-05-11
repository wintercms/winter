<!DOCTYPE html>
<html lang="<?= App::getLocale() ?>" class="no-js <?= $this->makeLayoutPartial('browser_detector') ?>">
    <head>
        <?= $this->makeLayoutPartial('head') ?>
        <?= $this->fireViewEvent('backend.layout.extendHead', ['layout' => 'default']) ?>
    </head>
    <body class="<?= $this->bodyClass ?>">
        <div id="layout-canvas">
            <?php if (\BackendAuth::isImpersonator()): ?>
                <div class="global-notice">
                    <div class="notice-content">
                        <span class="notice-text">
                            <span class="notice-icon wn-icon icon-exclamation-triangle"></span>
                            <?= e(trans('backend::lang.account.impersonating', [
                                'impersonator' => \BackendAuth::getImpersonator()->email,
                                'impersonatee' => \BackendAuth::getUser()->email,
                            ])); ?>
                        </span>
                    </div>
                    <a href="<?= Backend::url('backend/auth/signout') ?>" class="notice-action btn btn-secondary">
                        <?= e(trans('backend::lang.account.stop_impersonating')) ?>
                    </a>
                </div>
            <?php endif; ?>

            <div class="layout">

                <!-- Main Menu -->
                <div class="layout-row min-size">
                    <?= $this->makeLayoutPartial('mainmenu') ?>
                </div>

                <?php $flyoutContent = Block::placeholder('sidepanel-flyout') ?>

                <div class="layout-row">
                    <div class="layout flyout-container"
                        <?php if ($flyoutContent): ?>
                            data-control="flyout"
                            data-flyout-width="400"
                            data-flyout-toggle="#layout-sidenav"
                        <?php endif ?>
                    >
                        <?php if ($flyoutContent): ?>
                            <div class="layout-cell flyout"> <?= $flyoutContent ?></div>
                        <?php endif ?>

                        <!-- Side Navigation -->
                        <?= $this->makeLayoutPartial('sidenav') ?>

                        <!-- Side panel -->
                        <?php if ($sidePanelContent = Block::placeholder('sidepanel')): ?>
                            <div class="layout-cell w-350 hide-on-small" id="layout-side-panel" data-control="layout-sidepanel">
                                <?= $sidePanelContent ?>
                            </div>
                        <?php endif ?>

                        <!-- Content Body -->
                        <div class="layout-cell layout-container" id="layout-body">
                            <div class="layout-relative">

                                <div class="layout">
                                    <?php if ($breadcrumbContent = Block::placeholder('breadcrumb')): ?>
                                        <!-- Breadcrumb -->
                                        <div class="control-breadcrumb">
                                            <?= $breadcrumbContent ?>
                                        </div>
                                    <?php endif ?>

                                    <!-- Content -->
                                    <div class="layout-row">
                                        <?= Block::placeholder('body') ?>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- Flash Messages -->
        <div id="layout-flash-messages"><?= $this->makeLayoutPartial('flash_messages') ?></div>

    </body>
</html>
