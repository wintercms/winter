<?php
$composerPath = dirname(__DIR__, 3) . '/composer.json';
$composer = json_decode(file_get_contents($composerPath), true);

$packages = [
    'winter/wn-test-plugin' => 'dev-main',
    'winter/wn-blog-plugin' => 'dev-main',
    'winter/wn-blog-plugin' => 'dev-main',
    'winter/wn-workshop-theme' => 'dev-main',
];

// Install Winter packages
foreach ($packages as $package => $version) {
    if (!in_array($package, array_keys($composer['require']))) {
        $composer['require'][$package] = $version;
    }
}

file_put_contents(
    $composerPath,
    json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);
