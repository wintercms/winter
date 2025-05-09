<div class="loading-indicator-container size-small pull-right">
    <div class="control-pagination loading-indicator-container">
        <span class="page-iteration">
            <?= e(trans('backend::lang.list.pagination', ['from' => $pageFrom, 'to' => $pageTo, 'total' => $recordTotal])) ?>
        </span>
        <?php if ($pageLast > 1): ?>
            <?php if ($pageCurrent > 1): ?>
                <a
                    href="javascript:;"
                    class="page-first"
                    data-request="<?= $this->getEventHandler('onPaginate') ?>"
                    data-request-data="page: 1"
                    data-load-indicator="<?= e(trans('backend::lang.list.loading')) ?>"
                    title="<?= e(trans('backend::lang.list.first_page')) ?>"></a>
            <?php else: ?>
                <span
                    class="page-first"
                    title="<?= e(trans('backend::lang.list.first_page')) ?>"></span>
            <?php endif ?>
            <?php if ($pageCurrent > 1): ?>
                <a
                    href="javascript:;"
                    class="page-back"
                    data-request="<?= $this->getEventHandler('onPaginate') ?>"
                    data-request-data="page: <?= $pageCurrent-1 ?>"
                    data-load-indicator="<?= e(trans('backend::lang.list.loading')) ?>"
                    title="<?= e(trans('backend::lang.list.prev_page')) ?>"></a>
            <?php else: ?>
                <span
                    class="page-back"
                    title="<?= e(trans('backend::lang.list.prev_page')) ?>"></span>
            <?php endif ?>
            <input
                type="number"
                name="page"
                value="<?= $pageCurrent ?>"
                min="1"
                step="1"
                max="<?= $pageLast ?>"
                class="form-control input-sm"
                data-request="<?= $this->getEventHandler('onPaginate') ?>"
                data-track-input
                data-load-indicator="<?= e(trans('backend::lang.list.loading')) ?>"
                autocomplete="off"
                style="width: auto; padding-left: 5px; padding-right: 0; display: inline; text-align: center;" />
            <?php if ($pageLast > $pageCurrent): ?>
                <a
                    href="javascript:;"
                    class="page-next"
                    data-request-data="page: <?= $pageCurrent+1 ?>"
                    data-request="<?= $this->getEventHandler('onPaginate') ?>"
                    data-load-indicator="<?= e(trans('backend::lang.list.loading')) ?>"
                    title="<?= e(trans('backend::lang.list.next_page')) ?>"></a>
            <?php else: ?>
                <span
                    class="page-next"
                    title="<?= e(trans('backend::lang.list.next_page')) ?>"></span>
            <?php endif ?>
            <?php if ($pageLast > $pageCurrent): ?>
                <a
                    href="javascript:;"
                    class="page-last"
                    data-request-data="page: <?= $pageLast ?>"
                    data-request="<?= $this->getEventHandler('onPaginate') ?>"
                    data-load-indicator="<?= e(trans('backend::lang.list.loading')) ?>"
                    title="<?= e(trans('backend::lang.list.last_page')) ?>"></a>
            <?php else: ?>
                <span
                    class="page-last"
                    title="<?= e(trans('backend::lang.list.last_page')) ?>"></span>
            <?php endif ?>
        <?php endif ?>
    </div>
</div>
