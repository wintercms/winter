<div id="deleteFilteredPopup">
    <?= Form::open(['id' => 'deleteFiltered']) ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="popup">&times;</button>
            <h4 class="modal-title"><?= e(trans('system::lang.event_log.delete_filtered_link')) ?></h4>
        </div>

        <div class="modal-body clearfix">
            <?php if ($this->fatalError): ?>
                <p class="flash-message static error"><?= e($fatalError) ?></p>
            <?php endif ?>

            <?= $formWidget->render(); ?>

            <div class="loading-indicator-container">
                <div class="loading-indicator hidden">
                    <span></span>
                    <div><?= e(trans('system::lang.event_log.delete_filtered_loading')) ?></div>
                </div>

                <div id="deleteFilteredResults" class="form-group span-full"></div>
                <div id="deleteFilteredList" class="form-group span-full"></div>
            </div>

        </div>

        <div class="modal-footer">
            <button
                type="button"
                class="btn btn-default"
                data-dismiss="popup">
                <?= e(trans('backend::lang.form.cancel')) ?>
            </button>

            <button
                data-name="submit"
                class="btn btn-success hidden"
                data-request="onClearLogDelete"
                data-request-confirm="<?= e(trans('system::lang.event_log.delete_filtered_confirm')) ?>"
                data-request-success="$(this).trigger('close.oc.popup');"
                data-stripe-load-indicator>
                <?= e(trans('system::lang.event_log.delete_filtered_link')) ?>
            </button>
        </div>

    <?= Form::close() ?>
</div>

<script>
    $('#deleteFilteredPopup').on('popupComplete', function() {

        var $loadingIndicator = $('#deleteFilteredPopup .loading-indicator-container .loading-indicator');
        var $resultsInfos = $('#deleteFilteredResults');
        var $resultsList = $('#deleteFilteredList');

        var $inputMessage = $('#deleteFiltered :input[name="message"]');
        $inputMessage.defaultValue = $inputMessage.val();

        function refreshResults () {
            showLoadingIndicator();

            Snowboard.request('#deleteFiltered', 'onClearLogInfos', {
                success: (data) => {
                    const total = data.total;

                    if (total > 0) {
                        $('#deleteFiltered button[data-name="submit"]').removeClass('hidden');
                    } else {
                        $('#deleteFiltered button[data-name="submit"]').addClass('hidden');
                    }

                    hideLoadingIndicator();
                },
                error: (data) => {
                    hideLoadingIndicator()
                },
            });
        };

        function showLoadingIndicator () {
            $loadingIndicator.removeClass('hidden');
            $resultsInfos.addClass('hidden');
            $resultsList.addClass('hidden');
        }

        function hideLoadingIndicator () {
            $loadingIndicator.addClass('hidden');
            $resultsInfos.removeClass('hidden');
            $resultsList.removeClass('hidden');
        }

        function delay(callback, ms) {
            var timer = 0;
            return function() {
                var context = this, args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    callback.apply(context, args);
                }, ms || 0);
            };
        }

        // Monitor change on message field
        $inputMessage.keyup(delay(function (e) {
            if (e.target.value != e.target.defaultValue) {
                e.target.defaultValue = this.value;
                refreshResults();
            }
        }, 750));

        // Monitor change on datepicker fields
        $('#deleteFiltered').on('change.oc.formwidget', '.field-datepicker', delay(function (e) {
            if (e.target.name != 'message') {
                refreshResults();
            }
        }, 400));
    })
</script>
