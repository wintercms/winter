<!-- Text -->
<input
    type="text"
    name="<?= $field->getName() ?>"
    id="<?= $field->getId() ?>"
    value="<?= e($field->value) ?>"
    placeholder="<?= e(trans($field->placeholder)) ?>"
    class="form-control"
    autocomplete="off"
    <?= $field->getAttributes() ?>
    <?= $this->previewMode ? 'disabled' : '' ?>
/>
