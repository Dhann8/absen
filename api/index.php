<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

try {
    // Prepare writable storage directories in /tmp for Vercel serverless execution
    $storageDirs = [
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs',
        '/tmp/storage/bootstrap/cache',
    ];

    foreach ($storageDirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    putenv('APP_STORAGE=/tmp/storage');
    putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

    require __DIR__ . '/../vendor/autoload.php';

    /** @var \Illuminate\Foundation\Application $app */
    $app = require __DIR__ . '/../bootstrap/app.php';

    // Force storage path to /tmp/storage before booting request
    $app->useStoragePath('/tmp/storage');

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    error_log("Vercel Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    error_log($e->getTraceAsString());

    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><title>Deployment Exception</title><style>body{font-family:sans-serif;padding:2rem;background:#fef2f2;color:#991b1b;}pre{background:#fff;padding:1rem;border-radius:8px;overflow-x:auto;border:1px solid #fca5a5;color:#1e293b;}</style></head><body>';
    echo '<h2>Vercel Laravel Deployment Error</h2>';
    echo '<p style="font-size:1.1rem;"><strong>' . htmlspecialchars(get_class($e)) . ': ' . htmlspecialchars($e->getMessage()) . '</strong></p>';
    echo '<p>Location: <code>' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</code></p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</body></html>';
}
