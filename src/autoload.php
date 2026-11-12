<?php

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'App\\Models\\'   => __DIR__ . '/Models/',
        'App\\Services\\' => __DIR__ . '/Services/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    // Fallback untuk class tanpa namespace (misal: Media, Ebook, User)
    $fallbackPaths = [
        __DIR__ . '/Models/' . $class . '.php',
        __DIR__ . '/Services/' . $class . '.php',
    ];

    foreach ($fallbackPaths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});
