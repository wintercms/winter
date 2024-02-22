<div class="icon-container <?= $itemType ?>">
    <div class="icon-wrapper">
        <?php
            $itemIconClass = $this->itemTypeToIconClass($item, $itemType);
            $extension = substr(strtolower(pathinfo($item->path, PATHINFO_EXTENSION)), 0, 4) ?? '???';

            switch($extension)
            {
                case 'css';
                case 'less';
                case 'scss';
                    $labelColor = '#B73FD9'; break;
                case 'html';
                case 'xml';
                    $labelColor = '#EA9B47'; break;
                case 'js';
                case 'json';
                    $labelColor = '#A9A9A9'; break;
                case 'pdf';
                    $labelColor = '#E30713'; break;
                case 'txt';
                    $labelColor = '#248BD0'; break;
                case 'ai';
                    $labelColor = '#F29200'; break;
                case 'eps';
                    $labelColor = '#F9B234'; break;
                case 'psd';
                    $labelColor = '#2DAAE2'; break;
                case 'ttf';
                case 'otf';
                case 'woff';
                case 'woff2';
                    $labelColor = '#C4CA10'; break;
                case 'doc';
                case 'docx';
                case 'rtf';
                case 'odt';
                    $labelColor = '#0F70B7'; break;
                case 'csv';
                case 'ods';
                case 'xls';
                case 'xlsx';
                    $labelColor = '#3BAA34'; break;
                case 'odp';
                case 'ppt';
                case 'pptx';
                    $labelColor = '#D04526'; break;
                case 'rar';
                case 'tar';
                case 'zip';
                    $labelColor = '#363A56'; break;
                default;
                    $labelColor = '#576D7E'; break;
            }

        if ($itemIconClass == 'icon-file'): ?>
            <svg viewBox="0 0 250 250" xml:space="preserve"
                style="max-width:90%;max-height:90%;fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
                <path d="M62.5,0c-8.594,0 -15.625,7.031 -15.625,15.625l0,218.75c0,8.594 7.031,15.625 15.625,15.625l156.25,0c8.594,0 15.625,-7.031 15.625,-15.625l0,-171.875l-62.5,-62.5l-109.375,0Z" style="fill:#e2e5e7;fill-rule:nonzero;"/>
                <path d="M187.5,62.5l46.875,0l-62.5,-62.5l0,46.875c0,8.594 7.031,15.625 15.625,15.625Z" style="fill:#b0b7bd;fill-rule:nonzero;"/>
                <path d="M234.375,109.375l-46.875,-46.875l46.875,0l0,46.875Z" style="fill:#cad1d8;fill-rule:nonzero;"/>
                <path d="M195.313,210.938l-148.438,-0.001l0,7.813l148.438,0c4.296,0 7.812,-3.516 7.812,-7.813l-0,-7.812c-0,4.297 -3.516,7.813 -7.813,7.813Z" style="fill:#cad1d8;fill-rule:nonzero;"/>
                <!-- Colour label -->
                <path d="M203.125,203.125c0,4.297 -3.516,7.813 -7.813,7.813l-171.875,-0c-4.296,-0 -7.812,-3.516 -7.812,-7.813l-0,-78.125c-0,-4.297 3.516,-7.813 7.812,-7.813l171.875,0.001c4.297,-0.001 7.813,3.515 7.813,7.812l0,78.125Z" style="fill:<?= $labelColor ?>;fill-rule:nonzero;"/>
                <!-- Extension text node -->
                <text x="43%" y="170px" dominant-baseline="middle" text-anchor="middle" style="font-family:'ArialMT','Arial',sans-serif;font-size:4em;fill:#fff;"><?= strtoupper($extension) ?></text>
            </svg>
        <?php else :?>
            <i class="<?= $itemIconClass ?>"></i>
        <?php endif ?>
    </div>
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
