<?= Form::open(['class'=>'layout-relative dashboard-container']) ?>
    <div id="dashReportContainer" class="report-container loading">
        <!-- Loading -->
        <div class="loading-indicator-container">
            <div class="loading-indicator indicator-center">
                <span></span>
                <div><?= e(trans('backend::lang.list.loading')) ?></div>
            </div>
        </div>
    </div>
<?= Form::close() ?>

<?php Block::put('head'); ?>
<script>
Snowboard.ready(() => {
    Snowboard.request(null, 'onInitReportContainer', {
        success: () => {
            $('#dashReportContainer').removeClass('loading');
        },
    });
});
</script>
<?php Block::endPut(true); ?>
