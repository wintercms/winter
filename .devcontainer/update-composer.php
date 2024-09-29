<?php
$composer = json_decode(file_get_contents(dirname(__DIR__) . '/composer.json'), true);

// Install Winter Test plugin
if (
    !in_array('winter/wn-test-plugin', array_keys($composer['require']))
) {
    $composer['require']['winter/wn-test-plugin'] = 'dev-main';
}

// Install Winter Blog plugin
if (
    !in_array('winter/wn-blog-plugin', array_keys($composer['require']))
) {
    $composer['require']['winter/wn-blog-plugin'] = 'dev-main';
}

// Install Winter Pages plugin
if (
    !in_array('winter/wn-pages-plugin', array_keys($composer['require']))
) {
    $composer['require']['winter/wn-pages-plugin'] = 'dev-main';
}

// Install Workshop theme
if (
    !in_array('winter/wn-workshop-theme', array_keys($composer['require']))
) {
    $composer['require']['winter/wn-workshop-theme'] = 'dev-main';
}

file_put_contents(
    dirname(__DIR__) . '/composer.json',
    json_encode($composer, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK)
);