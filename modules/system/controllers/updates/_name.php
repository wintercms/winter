<?php
$icon = $record->icon;

if ($record->is_disabled) {
    $icon = 'icon-eye-slash';
} elseif ($record->disabledBySystem) {
    $icon = 'icon-exclamation';
} elseif ($record->orphaned) {
    $icon = 'icon-question';
} elseif ($record->is_frozen) {
    $icon = 'icon-lock';
}
?>
<span class="flex items-center">
    <i class="icon <?= $icon ?> mr-3 text-2xl"></i>
    <?= $value ?>
</span>
