<div
    class="calendar-container <?= $cssClasses ?>"
    data-control="calendar"
    data-first-day = '<?= $firstDay; ?>'
    data-initial-view = '<?= $initialView; ?>'
    data-display-modes = '<?= $availableDisplayModes; ?>'
    data-timezone = '<?= e($timezone); ?>'
    data-alias="<?= $this->alias; ?>"
    data-click-date="<?= $this->onClickDate ?>"
    data-editable="<?= $this->previewMode ? 'false' : 'true'; ?>"
>
    <div class="calendar-control loading-indicator-container indicator-center"></div>
</div>
