<?php
/**
 * @var \Winter\Storm\Database\Model $formModel
 * @var \Backend\Classes\FormField $field
 */
$action = 'button';
$handler = null;
$href = null;
$target = null;

if (!empty($field->config['href']) || filter_var($field->value, FILTER_VALIDATE_URL)) {
    $action = 'link';
    $href = $field->config['href'] ?? '';
    $target = $field->config['target'] ?? null;
    if ($formModel->hasAttribute($href)) {
        $href = $formModel->getAttribute($href);
    }
    if (filter_var($field->value, FILTER_VALIDATE_URL)) {
        $href = $field->value;
    }
} elseif (!empty($field->config['handler'])) {
    $action = 'popup';
    $handler = $field->config['handler'];
}

$element = $action === 'link' ? 'a' : 'button';
$label = $field->config['buttonLabel'] ?? '';
$buttonType = $field->config['buttonType'] ?? 'default';
$classes = implode(' ', array_filter([
    "btn btn-$buttonType",
    $field->config['buttonCssClass'] ?? ''
]));
$request = $field->config['request'] ?? '';

$loadingText = $field->config['loading'] ?? '';
$icon = $field->config['icon'] ?? '';
?>
<div class="loading-indicator-container">
    <?php if ($field->path): ?>
        <?= $this->controller->makePartial($field->path, [
            'formWidget' => $this,
            'formModel'  => $formModel,
            'formField'  => $field,
            'formValue'  => $field->value,
            'model'      => $formModel,
            'field'      => $field,
            'value'      => $field->value,
            'action'     => $action,
            'element'    => $element,
            'label'      => $label,
            'buttonType' => $buttonType,
            'classes'    => $classes,
            'handler'    => $handler,
            'request'    => $request,
            'href'       => $href,
            'target'     => $target,
            'loading'    => $loadingText,
            'icon'       => $icon,
        ]) ?>
    <?php else: ?>
        <<?= e($element); ?>
            class="<?= e($classes); ?>"
            data-load-indicator<?= !empty($loadingText) ? '="' . e(trans($loadingText)) . '"' : ''; ?>
            <?= $action === 'popup' ? 'data-control="popup"' : ''; ?>
            <?= !empty($handler) ? 'data-handler="' . e($handler) . '"' : ''; ?>
            <?= !empty($request) ? 'data-request="' . e($request) . '"' : ''; ?>
            <?= !empty($href) ? 'href="' . e($href) . '"' : ''; ?>
            <?= !empty($target) ? 'target="' . e($target) . '"' : ''; ?>
        >
            <?= !empty($icon) ? '<i class="' . e($icon) . '"></i>' : '' ?>
            <?= e(trans($label)); ?>
        </<?= e($element); ?>>
    <?php endif; ?>
</div>
