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
        <div class="repeater-group-search-container" style="padding: 10px 15px; border-bottom: 1px solid #e0e0e0;">
            <div style="position: relative;">
                <label for="repeater-group-search-<?= $this->getId() ?>" class="sr-only">Search items</label>
                <i class="icon-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #999; pointer-events: none; z-index: 10;"></i>
                <input type="text"
                    id="repeater-group-search-<?= $this->getId() ?>"
                    class="form-control repeater-group-search"
                    placeholder="Search items..."
                    autocomplete="off"
                    style="padding-left: 32px; padding-right: 32px; width: 100%;">
                <button type="button" class="repeater-group-search-clear" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: transparent; border: none; padding: 4px 6px; cursor: pointer; display: none; z-index: 10; color: #999;">
                    <i class="icon-close" style="font-size: 14px;"></i>
                </button>
            </div>
        </div>
        <div class="repeater-group-no-results" style="display: none; padding: 20px; text-align: center; color: #999;">
            No items found
        </div>
        <div class="popover-fixed-height repeater-group-items-container" style="min-width: 500px; max-width: 800px;">
            <div class="control-scrollpad" data-control="scrollpad">
                <div class="scroll-wrapper">

                    <div class="control-filelist filelist-hero repeater-group-grid" data-control="filelist" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 8px; padding: 15px;">
                        <?php foreach ($groupDefinitions as $item): ?>
                            <div class="repeater-group-item">
                                <a
                                    href="javascript:;"
                                    data-repeater-add
                                    data-request="<?= $this->getEventHandler('onAddItem') ?>"
                                    data-request-data="_repeater_group: '<?= $item['code'] ?>'"
                                    style="display: flex; height: 100%; align-items: center; gap: 16px; padding: 12px 12px 12px 16px; border: 1px solid #e0e0e0; border-radius: 4px; text-decoration: none; color: inherit; transition: all 0.2s;">
                                    <i class="<?= $item['icon'] ?>" style="width: 1em; text-align: center; font-size: 24px; color: #666; margin-top: 2px; flex-shrink: 0;"></i>
                                    <div style="flex: 1; min-width: 0;">
                                        <span class="title" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 2px;"><?= e(trans($item['name'])) ?></span>
                                        <span class="description" style="font-size: 11px; color: #999; display: block; line-height: 1.3;"><?= e(trans($item['description'])) ?></span>
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
