<div class="field-repeater"
    data-control="fieldrepeater"
    <?= $titleFrom ? 'data-title-from="'.$titleFrom.'"' : '' ?>
    <?= $minItems ? 'data-min-items="'.$minItems.'"' : '' ?>
    <?= $maxItems ? 'data-max-items="'.$maxItems.'"' : '' ?>
    <?= $style ? 'data-style="'.$style.'"' : '' ?>
    data-mode="<?= $mode ?>"
    <?php if ($mode === 'grid'): ?>
    data-columns="<?= $columns ?>"
    <?php endif ?>
    <?php if ($sortable): ?>
    data-sortable="true"
    data-sortable-container="#<?= $this->getId('items') ?>"
    data-sortable-handle=".<?= $this->getId('items') ?>-handle"
    <?php endif; ?>
>
    <?php if (!$this->previewMode): ?>
        <input type="hidden" name="<?= $this->getFieldName(); ?>">
    <?php endif ?>

    <ul id="<?= $this->getId('items') ?>" class="field-repeater-items">
        <?php foreach ($formWidgets as $index => $widget): ?>
            <?= $this->makePartial('repeater_item', [
                'widget' => $widget,
                'indexValue' => $index,
            ]) ?>
        <?php endforeach ?>

        <?= $this->makePartial('repeater_add_item') ?>
    </ul>

    <?php if (!$this->previewMode): ?>
        <input type="hidden" name="<?= $this->alias; ?>_loaded" value="1">
    <?php endif ?>

    <script type="text/template" data-group-palette-template>
        <div class="popover-head">
            <h3><?= e(trans($prompt)) ?></h3>
            <button type="button" class="close"
                data-dismiss="popover"
                aria-hidden="true">&times;</button>
        </div>
        <div class="repeater-group-search-container">
            <div>
                <label for="repeater-group-search-<?= $this->getId() ?>" class="sr-only">Search items</label>
                <i class="icon-search"></i>
                <input type="text"
                    id="repeater-group-search-<?= $this->getId() ?>"
                    class="form-control repeater-group-search"
                    placeholder="Search items..."
                    autocomplete="off">
                <button type="button" class="repeater-group-search-clear">
                    <i class="icon-close"></i>
                </button>
            </div>
        </div>
        <div class="repeater-group-no-results">
            No items found
        </div>
        <div class="popover-fixed-height repeater-group-items-container">
            <div class="control-scrollpad" data-control="scrollpad">
                <div class="scroll-wrapper">

                    <div class="control-filelist filelist-hero repeater-group-grid" data-control="filelist">
                        <?php foreach ($groupDefinitions as $item): ?>
                            <div class="repeater-group-item">
                                <a
                                    href="javascript:;"
                                    data-repeater-add
                                    data-request="<?= $this->getEventHandler('onAddItem') ?>"
                                    data-request-data="_repeater_group: '<?= $item['code'] ?>'">
                                    <i class="<?= $item['icon'] ?>"></i>
                                    <div>
                                        <span class="title"><?= e(trans($item['name'])) ?></span>
                                        <span class="description"><?= e(trans($item['description'])) ?></span>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach ?>
                    </div>

                </div>
            </div>
        </div>
    </script>

</div>
