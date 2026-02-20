<?php $label = object_get($this->config, 'label'); ?>

<fieldset class="fieldset">
    <?php if ($label): ?>
        <legend><?= trans($label) ?></legend>
    <?php endif ?>

    <?= $this->formWidget->render(['section' => 'outside']) ?>
</fieldset>
