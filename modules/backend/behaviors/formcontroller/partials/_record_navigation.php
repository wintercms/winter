<?php
    /** @var array{previous: mixed, next: mixed, current: int, total: int} $navigation */
    $previousUrl = $navigation['previous'] !== null
        ? $this->actionUrl($navigationContext, $navigation['previous'])
        : null;
    $nextUrl = $navigation['next'] !== null
        ? $this->actionUrl($navigationContext, $navigation['next'])
        : null;
    $chevronUp = '<svg viewBox="0 0 16 16" width="13" height="13" aria-hidden="true"><path d="M4 10l4-4 4 4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    $chevronDown = '<svg viewBox="0 0 16 16" width="13" height="13" aria-hidden="true"><path d="M4 6l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
?>
<div class="form-record-nav" role="navigation" aria-label="<?= e(trans('backend::lang.form.record_navigation')) ?>">
    <span class="form-record-nav-position">
        <?= e($navigation['current']) ?>&nbsp;/&nbsp;<?= e($navigation['total']) ?>
    </span>
    <span class="form-record-nav-group">
        <?php if ($previousUrl): ?>
            <a href="<?= e($previousUrl) ?>" class="form-record-nav-btn" title="<?= e(trans('backend::lang.form.previous_record')) ?>" data-hotkey="ctrl+up, cmd+up"><?= $chevronUp ?></a>
        <?php else: ?>
            <span class="form-record-nav-btn is-disabled" aria-disabled="true"><?= $chevronUp ?></span>
        <?php endif ?>
        <?php if ($nextUrl): ?>
            <a href="<?= e($nextUrl) ?>" class="form-record-nav-btn" title="<?= e(trans('backend::lang.form.next_record')) ?>" data-hotkey="ctrl+down, cmd+down"><?= $chevronDown ?></a>
        <?php else: ?>
            <span class="form-record-nav-btn is-disabled" aria-disabled="true"><?= $chevronDown ?></span>
        <?php endif ?>
    </span>
</div>
