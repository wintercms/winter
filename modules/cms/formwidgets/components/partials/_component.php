<div class="layout-cell <?= e($component->componentCssClass) ?> <?= $component->isHidden ? 'hidden' : null ?>">
    <div
        class="<?= 'wn-'.$component->pluginIcon ?> layout-relative"
        <?php if ($component->inspectorEnabled): ?>
            data-inspectable
        <?php endif ?>
        data-inspector-title="<?= $name = e($this->getComponentName($component)) ?>"
        data-inspector-description="<?= $description = e($this->getComponentDescription($component)) ?>"
        data-inspector-config="<?= e($this->getComponentsPropertyConfig($component)) ?>"
        data-inspector-class="<?= get_class($component) ?>"
    >
        <span class="name"><?= $name ?></span>
        <span class="description"><?= $description ?></span>
        <span class="alias wn-icon-code"><?= e($component->alias) ?></span>
        <input type="hidden" data-component-index="<?= $index ?>">
        <input type="hidden" name="component_properties[<?= $index ?>]" data-inspector-values value="<?= e($this->getComponentPropertyValues($component)) ?>" />
        <input type="hidden" name="component_names[<?= $index ?>]" value="<?= e($component->name) ?>" />
        <input type="hidden" name="component_aliases[<?= $index ?>]" value="<?= e($component->alias) ?>" />
        <a href="#" class="remove">&times;</a>
    </div>
</div>
