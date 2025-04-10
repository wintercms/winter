<!-- IconPicker -->
<?php if ($this->previewMode): ?>
    <span class="form-control">
        <i class="<?= e($field->getLoadValue()) ?>"></i>
    </span>
<?php else: ?>
    <?php $icon = $field->getLoadValue() ? e($field->getLoadValue()) : ($field->config->default ?? ''); ?>
    <div id="<?= $field->getId() ?>"
         data-control="iconpicker"
         data-label="Icon Picker"
         data-prop-value="<?= $icon ?>"
         data-name="<?= $field->getFieldName() ?>"
         data-alias="<?= $field->alias ?>"
         data-event-handler="<?= $this->getEventHandler('onLoadIconLibrary') ?>"
    >
        <div class="input-group">
            <span class="input-group-addon" style="cursor: pointer">
                <i class="<?= $icon ?>"></i>
            </span>
            <input type="text"
                   class="form-control"
                   name="<?= $field->getFieldName() ?>"
                   value="<?= $icon ?>"
            >
        </div>
    </div>
<?php endif ?>
