<?php if (!$this->previewMode): ?>
    <li
        id="<?= $this->getId('add-item') ?>"
        class="field-repeater-add-item loading-indicator-container indicator-center"
        <?php if ($mode === 'grid'): ?>
        style="min-height: <?= $rowHeight ?>px"
        <?php endif ?>
    >
        <?php if ($useGroups): ?>
            <a
                href="javascript:;"
                data-repeater-add-group
                data-load-indicator>
                <span><?= e(trans($prompt)) ?></span>
            </a>
        <?php else: ?>
            <a
                href="javascript:;"
                data-repeater-add
                data-request="<?= $this->getEventHandler('onAddItem') ?>"
                data-load-indicator>
                <span><?= e(trans($prompt)) ?></span>
            </a>
        <?php endif ?>
    </li>
<?php endif ?>
