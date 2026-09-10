<?php

// Ensure writable directories in /tmp for serverless execution (Vercel / AWS Lambda)
$storagePath = '/tmp/storage';
$tmpDirs = [
    $storagePath,
    $storagePath . '/app',
    $storagePath . '/app/public',
    $storagePath . '/framework',
    $storagePath . '/framework/cache',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/views',
    $storagePath . '/logs',
    '/tmp/views',
    '/tmp/cache',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Ensure serverless environment variables and cache locations
$envVars = [
    'APP_STORAGE'         => $storagePath,
    'VIEW_COMPILED_PATH'  => $storagePath . '/framework/views',
    'APP_CONFIG_CACHE'    => '/tmp/config.php',
    'APP_EVENTS_CACHE'    => '/tmp/events.php',
    'APP_PACKAGES_CACHE'  => '/tmp/packages.php',
    'APP_ROUTES_CACHE'    => '/tmp/routes.php',
    'APP_SERVICES_CACHE'  => '/tmp/services.php',
    'SESSION_DRIVER'      => 'cookie',
    'CACHE_STORE'         => 'array',
    'LOG_CHANNEL'         => 'stderr',
];

foreach ($envVars as $key => $val) {
    if (!isset($_ENV[$key]) && !getenv($key)) {
        putenv("$key=$val");
        $_ENV[$key] = $val;
        $_SERVER[$key] = $val;
    }
}

// Forward all requests to public/index.php
require __DIR__ . '/../public/index.php';
