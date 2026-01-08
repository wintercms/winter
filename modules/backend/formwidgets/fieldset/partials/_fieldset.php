<?php $placeholder = object_get($this->config, 'placeholder'); ?>

<fieldset class="fieldset">
    <?php if ($placeholder): ?>
        <legend><?= trans($placeholder) ?></legend>
    <?php endif ?>

    <?= $this->formWidget->render() ?>
</fieldset>
