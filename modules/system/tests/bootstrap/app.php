<?php

$baseDir = realpath(__DIR__ . '/../../../..');

/*
 * Winter autoloader
 */
require $baseDir . '/bootstrap/autoload.php';

/*
 * Fallback autoloader
 */
$loader = new Winter\Storm\Support\ClassLoader(
    new Winter\Storm\Filesystem\Filesystem,
    $baseDir,
    $baseDir . '/storage/framework/classes.php'
);

$loader->register();

/*
 * Manually register all module classes for autoloading
 */
foreach (glob($baseDir . '/modules/*', GLOB_ONLYDIR) as $modulePath) {
    $loader->autoloadPackage(basename($modulePath), $modulePath);
}

/*
 * Manually register System aliases
 */
foreach (require(__DIR__ . '/../../aliases.php') as $alias => $class) {
    if (!class_exists($alias)) {
        class_alias($class, $alias);
    }
}

/*
 * Manually register all plugin classes for autoloading
 */
$dirPath = $baseDir . '/plugins';
if (is_dir($dirPath)) {
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dirPath, FilesystemIterator::FOLLOW_SYMLINKS)
    );
    $it->setMaxDepth(2);
    $it->rewind();

    while ($it->valid()) {
        if (($it->getDepth() > 1) && $it->isFile() && (strtolower($it->getFilename()) === "plugin.php")) {
            $filePath = dirname($it->getPathname());
            $pluginName = basename($filePath);
            $vendorName = basename(dirname($filePath));

            $loader->autoloadPackage($vendorName . '\\' . $pluginName, $filePath);
        }

        $it->next();
    }
}
