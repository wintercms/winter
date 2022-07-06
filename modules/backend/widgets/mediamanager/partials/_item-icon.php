<div class="icon-container <?= $itemType ?>">
    <div class="icon-wrapper"><i class="<?= $this->itemTypeToIconClass($item, $itemType) ?>"></i></div>
    <?php if (
        $itemType == System\Classes\MediaLibraryItem::FILE_TYPE_IMAGE
        && $thumbnailUrl = $this->getResizedImageUrl($item->path, $thumbnailParams)
    ): ?>
        <div>
            <?php if (!$thumbnailUrl): ?>
                <div
                    class="image-placeholder"
                    data-width="<?= $thumbnailParams['width'] ?>"
                    data-height="<?= $thumbnailParams['height'] ?>"
                    data-path="<?= e($item->path) ?>"
                    data-last-modified="<?= $item->lastModified ?>"
                    id="<?= $this->getPlaceholderId($item) ?>"
                >
                    <div class="icon-wrapper"><i class="<?= $this->itemTypeToIconClass($item, $itemType) ?>"></i></div>
                </div>
            <?php else: ?>
                <?= $this->makePartial('thumbnail-image', [
                    'imageUrl' => $thumbnailUrl,
                ]) ?>
            <?php endif ?>
        </div>
    <?php endif ?>
</div>
