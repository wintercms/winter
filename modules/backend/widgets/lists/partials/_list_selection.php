<div
    class="list-selection"
    role="status"
    aria-live="polite"
    data-selection-fingerprint="<?= e($selectionFingerprint) ?>"
    data-confirm-all="<?= e($selectionTotal
        ? trans('backend::lang.list.selection_confirm_all', ['total' => $selectionTotal])
        : trans('backend::lang.list.selection_confirm_all_unknown')) ?>">
    <span class="list-selection-page">
        <?= e(trans_choice('backend::lang.list.selection_page', count($records), ['count' => count($records)])) ?>
        <a href="javascript:;" class="list-selection-select-all">
            <?= e($selectionTotal
                ? trans('backend::lang.list.selection_select_all', ['total' => $selectionTotal])
                : trans('backend::lang.list.selection_select_all_unknown')) ?>
        </a>
    </span>
    <span class="list-selection-all">
        <?= e($selectionTotal
            ? trans('backend::lang.list.selection_all', ['total' => $selectionTotal])
            : trans('backend::lang.list.selection_all_unknown')) ?>
        <a href="javascript:;" class="list-selection-clear">
            <?= e(trans('backend::lang.list.selection_clear')) ?>
        </a>
    </span>
</div>
