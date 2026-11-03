<!-- Time -->
<?php /* Same as _picker_date.php — the field's <label for> targets the hidden data-locker
        input, leaving this visible text box without an accessible name (WCAG 1.3.1 / 4.1.2).
        "(time)" distinguishes it from the date box in datetime mode, where both share the label. */ ?>
<div class="input-with-icon right-align">
    <i class="icon icon-clock-o" aria-hidden="true"></i>
    <input
        type="text"
        id="<?= $this->getId('time') ?>"
        class="form-control align-right"
        autocomplete="off"
        <?php if ($field->label): ?>
            aria-label="<?= e(trans($field->label)) ?> (time)"
        <?php endif ?>
        <?= $field->getAttributes() ?>
        data-timepicker />
</div>
