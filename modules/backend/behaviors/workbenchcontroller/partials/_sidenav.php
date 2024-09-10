<?php if (count($this->getSectionNavigation())): ?>
<div class="layout-cell layout-sidenav-container">
    <div class="layout-relative">
        <nav
            id="layout-sidenav"
            class="layout-sidenav bg-p"
            data-active-class="active"
            data-control="sidenav"
        >
            <ul class="nav">
                <?php foreach ($this->getSectionNavigation() as $section => $item): ?>
                    <li
                        data-section="<?= $section ?>"
                        <?= Html::attributes($item->attributes ?? []) ?>
                    >
                        <a href="javascript:;" data-change-section="<?= $section ?>">
                            <span class="nav-icon">
                                <?php if (is_null($item->icon) && is_null($item->iconSvg)): ?>
                                    <i class="icon-cog"></i>
                                <?php elseif ($item->iconSvg ?? false): ?>
                                    <img class="svg-icon" src="<?= Url::asset($item->iconSvg) ?>">
                                <?php else: ?>
                                    <i class="icon-<?= $item->icon ?>"></i>
                                <?php endif ?>
                            </span>
                            <span class="nav-label">
                                <?= e(trans($item->label)) ?>
                            </span>
                            <span
                                class="counter empty"
                                data-counter="<?= $section ?>"
                            >
                            </span>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </nav>
    </div>
</div>
<?php endif ?>
