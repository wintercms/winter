<div
    class="calendar-container"
    data-control="calendar"
    data-display-modes = '<?= $availableDisplayModes; ?>'
    data-alias="<?= $this->alias; ?>"
    data-click-date="<?= $this->onClickDate ?>"
    data-editable="<?= !$this->previewMode; ?>"
>
    <div class="calendar-control loading-indicator-container indicator-center"></div>
</div>
