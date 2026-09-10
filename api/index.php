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
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Set environment variable for storage if running in serverless environment
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    putenv('APP_STORAGE=' . $storagePath);
    $_ENV['APP_STORAGE'] = $storagePath;
    $_SERVER['APP_STORAGE'] = $storagePath;
}

// Forward all requests to public/index.php
require __DIR__ . '/../public/index.php';
