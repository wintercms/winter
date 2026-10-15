<!-- Date -->
<?php /* The field's <label for> targets the hidden data-locker input, leaving this
        visible text box without an accessible name (WCAG 1.3.1 / 4.1.2). Name it directly. */ ?>
<div class="input-with-icon right-align">
    <i class="icon icon-calendar-o" aria-hidden="true"></i>
    <input
        type="text"
        id="<?= $this->getId('date') ?>"
        class="form-control align-right"
        autocomplete="off"
        <?php if ($field->label): ?>
            aria-label="<?= e(trans($field->label)) ?>"
        <?php endif ?>
        <?= $field->getAttributes() ?>
        data-datepicker />
</div>
