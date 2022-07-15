<div class="relation-widget" id="<?= $this->getId() ?>">
    <?= $this->makePartial('~/modules/backend/widgets/form/partials/_field_'.$field->type.'.php', ['field' => $field]) ?>
</div>