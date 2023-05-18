<!-- Email -->
<input
    type="email"
    name="<?= $field->getName() ?>"
    id="<?= $field->getId() ?>"
    class="form-control"
    value="<?= e($field->value) ?>"
    placeholder="<?= e(trans($field->placeholder)) ?>"
    <?= $this->previewMode ? 'disabled' : '' ?>
    <?= $field->getAttributes() ?>
>
