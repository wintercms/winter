<?= Block::put('sidepanel') ?>
    <?php if (!$this->fatalError): ?>
        <?= $this->makePartial('sidepanel') ?>
    <?php endif ?>
<?= Block::endPut() ?>
