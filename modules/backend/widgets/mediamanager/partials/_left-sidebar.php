<?php if ($this->getFilterDisplay()): ?>
    <div id="<?= $this->getId('filters') ?>">
        <?= $this->makePartial('filters') ?>
    </div>
<?php endif; ?>

<?= $this->makePartial('sorting') ?>
