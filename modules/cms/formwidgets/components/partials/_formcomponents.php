<div class="control-componentlist bg-s">
    <?php foreach ($components as $index => $component): ?>
        <?php if ($component->isHidden): ?>
            <?= $this->makePartial('component', ['component' => $component, 'index' => $index]) ?>
        <?php endif ?>
    <?php endforeach ?>

    <div class="components" data-control="toolbar">
        <div class="layout">
            <?php foreach ($components as $index => $component): ?>
                <?php if (!$component->isHidden): ?>
                    <?= $this->makePartial('component', ['component' => $component, 'index' => $index]) ?>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    </div>
</div>
