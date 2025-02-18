<?php
$menu = \Backend\Facades\BackendMenu::getActiveMainMenuItem();
$context = \Backend\Facades\BackendMenu::getContext();
$sideMenu = $menu->sideMenu[$context->sideMenuCode ?? null] ?? null;
?>
<?php if ($menu): ?>
<ul>
    <li><a href="<?= $menu->url ?>"><?= e(trans($menu->label)) ?></a></li>
    <?php if ($sideMenu): ?>
        <li><a href="<?= $sideMenu->url ?>"><?= e(trans($sideMenu->label)) ?></a></li>
    <?php endif; ?>
    <li><?= e(trans($this->pageTitle)) ?></li>
</ul>
<?php endif; ?>
