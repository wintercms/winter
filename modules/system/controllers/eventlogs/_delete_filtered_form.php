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

            <div id="deleteFilteredResults" class="form-group span-full"></div>
            <div id="deleteFilteredList" class="form-group span-full"></div>

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
                data-dismiss="popup"
                data-stripe-load-indicator>
                <?= e(trans('system::lang.event_log.delete_filtered_link')) ?>
            </button>
        </div>

    <?= Form::close() ?>
</div>

<script>
    $('#deleteFilteredPopup').on('popupComplete', function() {
        function refreshResults () {
            Snowboard.request('#deleteFiltered', 'onClearLogInfos', {
                success: (data) => {
                    const total = data.total;

                    if (total > 0) {
                        $('#deleteFiltered button[data-name="submit"]').removeClass('hidden');
                    } else {
                        $('#deleteFiltered button[data-name="submit"]').addClass('hidden');
                    }
                },
            });
        };

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

        $('#deleteFiltered input:not(:button)').each(function () {
            this.defaultValue = this.value;

            if (this.type == 'text') {
                $(this).keyup(delay(function (e) {
                    if (e.target.value != e.target.defaultValue) {
                        e.target.defaultValue = this.value;
                        refreshResults();
                    }
                }, 750));
            } else {
                $(this).on('change', function() {
                    setTimeout(
                        refreshResults(),
                        1000
                    )
                });
            }

        });
    })
</script>
