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
    ></div>
    <script type="application/json"><?= json_encode($fontLibraries) ?></script>
<?php endif ?>



