<?php
$menu = \Backend\Facades\BackendMenu::getActiveMainMenuItem();
$context = \Backend\Facades\BackendMenu::getContext();
$sideMenu = $menu->sideMenu[$context->sideMenuCode ?? null] ?? null;
$settingsManager = \System\Classes\SettingsManager::instance();
$settingsContext = $settingsManager->getContext();
$settingsItem = null;
if ($settingsContext) {
    $settingsItem = $settingsManager->findSettingItem($settingsContext->owner, $settingsContext->itemCode);
}
?>
<?php if ($menu): ?>
<ul>
    <li><a href="<?= $menu->url ?>"><?= e(trans($menu->label)) ?></a></li>
    <?php if ($sideMenu && $sideMenu->url !== $menu->url): ?>
        <li><a href="<?= $sideMenu->url ?>"><?= e(trans($sideMenu->label)) ?></a></li>
    <?php elseif ($settingsItem): ?>
        <li><a href="<?= $settingsItem->url ?>"><?= e(trans($settingsItem->label)) ?></a></li>
    <?php endif; ?>
    <li><?= e(trans($this->pageTitle)) ?></li>
</ul>
<?php endif; ?>
