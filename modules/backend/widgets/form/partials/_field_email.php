<!-- Email -->
<div class="input-group static">
    <span class="input-group-addon">
        <i class="empty wn-icon-envelope"></i>
    </span>
    <?php if ($this->previewMode): ?>
        <?php if ($field->value): ?>
            <a
                href="mailto:<?= e($field->value) ?>"
                target="_blank"
                rel="noopener noreferrer"
                class="form-control"
            >
                <?= e($field->value) ?>
            </a>
        <?php else: ?>
            <span class="form-control">&nbsp;</span>
        <?php endif ?>
    <?php else: ?>
        <input
            type="email"
            name="<?= $field->getName() ?>"
            id="<?= $field->getId() ?>"
            value="<?= e($field->value) ?>"
            placeholder="<?= e(trans($field->placeholder)) ?>"
            class="form-control"
            <?= $field->getAttributes() ?>
        />
    <?php endif ?>
</div>
