<div
    id="<?= $this->getId() ?>"
    class="
        field-mediafinder
        style-image-single
        is-image
        <?php if ($value): ?>
            is-populated
        <?php endif ?>
        <?php if ($this->previewMode): ?>
            is-preview
        <?php endif ?>
    "
    data-control="mediafinder"
    data-mediafinder-mode="image"
    data-thumbnail-width="<?= $imageWidth ?: '0' ?>"
    data-thumbnail-height="<?= $imageHeight ?: '0' ?>"
    <?= $field->getAttributes() ?>
>

    <!-- Find Button -->
    <a href="javascript:;" class="find-button">
        <span class="find-button-icon wn-icon-image"></span>
    </a>

    <!-- Existing value -->
    <div class="find-object">
        <div class="icon-container">
            <img data-find-image src="<?= $imageUrl ?>" alt="" />
        </div>

        <?php if (!$imageExists && !empty($imageUrl)): ?>
            <p data-find-error class="help-block">
                <?= e(trans('backend::lang.mediafinder.no_image')) ?>
            </p>
        <?php endif; ?>

        <div class="info">
            <h4 class="filename">
                <span data-find-file-name><?= e(ltrim($value, '/')) ?></span>
                <a href="javascript:;" class="find-remove-button">
                    <i class="icon-times"></i>
                </a>
            </h4>
        </div>
    </div>

    <!-- Data locker -->
    <input
        type="hidden"
        name="<?= $field->getName() ?>"
        id="<?= $field->getId() ?>"
        value="<?= e($value) ?>"
        data-find-value
        />
</div>
