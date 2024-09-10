<?php if ($items): ?>
    <ul>
        <?php foreach ($items as $key => $item): ?>
            <li
                class="item"
                data-item="<?= $item['id'] ?>"
            >
                <a
                    href="javascript:;"
                    data-control="dragvalue"
                >
                    <span class="title"><?= e($item['title']) ?></span>
                    <span class="description">
                        <?= e($item['description']) ?>
                    </span>
                    <span class="borders"></span>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
<?php else: ?>
    <p class="no-data"><?= e(trans($this->noRecordsMessage)) ?></p>
<?php endif ?>
