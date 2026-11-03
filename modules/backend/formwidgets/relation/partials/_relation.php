<div
    id="<?= $this->getId() ?>"
    data-control="relation"
    class="relation-widget"
>
    <?= $this->makePartial('~/modules/backend/widgets/form/partials/_field_'.$field->type.'.php', ['field' => $field]) ?>
</div>
