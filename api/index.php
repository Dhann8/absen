<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Prepare writable storage directories in /tmp for Vercel serverless environment
$storageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Register composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel application
$app = require __DIR__ . '/../bootstrap/app.php';

// Set storage path to writable /tmp directory
$app->useStoragePath('/tmp/storage');

// Handle HTTP request
$app->handleRequest(Request::capture());
