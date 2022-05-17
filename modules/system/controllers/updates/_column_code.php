<?php
$icon = null;

if ($record->is_disabled) {
    $icon = 'eye-slash';
} elseif ($record->disabledBySystem) {
    $icon = 'exclamation';
} elseif ($record->orphaned) {
    $icon = 'question';
} elseif ($record->is_frozen) {
    $icon = 'lock';
}
?>
<span class="<?= $icon ? 'wn-icon-'.$icon : '' ?>">
    <?= $value ?>
</span>
