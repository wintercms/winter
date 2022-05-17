<a
    href="javascript:;"
    class="btn btn-sm btn-secondary wn-icon-unlink"
    data-request="onRelationButtonUnlink"
    data-request-success="$.wn.relationBehavior.changed('<?= e($relationField) ?>', 'removed')"
    data-request-confirm="<?= e(trans('backend::lang.relation.unlink_confirm')) ?>"
    data-stripe-load-indicator>
    <?= e(trans($text)) ?>
</a>
