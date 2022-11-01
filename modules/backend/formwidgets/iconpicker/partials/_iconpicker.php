<!-- IconPicker -->
<?php if ($this->previewMode): ?>
    <span class="form-control">
        <i class="<?= e($field->getLoadValue()) ?>"></i>
    </span>
<?php else: ?>
    <div id="<?= $field->getId() ?>"
         data-control="iconpicker"
         data-label="Icon Picker"
         data-prop-value="<?= e($field->getLoadValue()) ?>"
         data-name="<?= $field->getFieldName() ?>"
    >
        <div class="input-group">
            <span class="input-group-addon" style="cursor: pointer">
                <i class="<?= $field->getLoadValue() ? e($field->getLoadValue()) : 'far icon-address-book' ?>"></i>
            </span>
            <input type="text" class="form-control" value="<?= $field->getLoadValue() ? e($field->getLoadValue()) : 'far icon-address-book' ?>">
        </div>
    </div>
<?php endif ?>
