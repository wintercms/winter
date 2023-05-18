<!-- Email -->
<input
    type="email"
    name="<?= $field->getName() ?>"
    id="<?= $field->getId() ?>"
    class="form-control"
    value="<?= e($field->value) ?>"
    placeholder="<?= e(trans($field->placeholder)) ?>"
    <?= $field->getAttributes() ?>
    <?= $this->previewMode ? 'disabled' : '' ?>
/>
