<div class="report-container">
    <input type="hidden" value="<?= e($this->alias) ?>" data-container-alias />

    <ul
        id="<?= e($this->getId('container-list')) ?>"
        class="<?= $this->canAddAndDelete ? 'add-delete' : null ?>"
        data-control="report-container">
        <?= $this->makePartial('widget_list') ?>
    </ul>

    <?php if ($this->canAddAndDelete): ?>
        <div id="<?= e($this->getId('container-toolbar')) ?>" data-container-toolbar>
            <?= $this->makePartial('widget_toolbar') ?>
        </div>
    <?php endif ?>
</div>
