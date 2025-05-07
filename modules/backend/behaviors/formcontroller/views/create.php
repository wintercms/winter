<?php

// Decide which layout we should be rendering
$layout = $this->formLayout ?? $formConfig->formLayout ?? null;
if (!in_array($layout, ['standard', 'sidebar', 'fancy'])) {
    $layout = 'standard';
}

// If required, set the appropriate body classes
$this->bodyClass = match ($layout) {
    'fancy' => 'fancy-layout compact-container breadcrumb-flush breadcrumb-fancy',
    'sidebar' => 'compact-container',
    default => '',
};

// Define layout mode view path for inclusion
$this->appendViewPath(sprintf('%s/create/%s', __DIR__, $layout));

// Render the form layout
echo $this->makePartial(sprintf('create/%s.php', $layout));
