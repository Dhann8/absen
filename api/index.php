<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

define('LARAVEL_START', microtime(true));

try {
    // 1. Definisikan direktori writable di /tmp
    $storageDirs = [
        '/tmp/storage/app',
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs',
        '/tmp/bootstrap/cache',
    ];

    foreach ($storageDirs as $dir) {
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    // Set di putenv, $_ENV, dan $_SERVER agar konsisten dibaca Laravel & config()
    $envVars = [
        'APP_STORAGE' => '/tmp/storage',
        'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
        'APP_CONFIG_CACHE' => '/tmp/bootstrap/cache/config.php',
        'APP_SERVICES_CACHE' => '/tmp/bootstrap/cache/services.php',
        'APP_PACKAGES_CACHE' => '/tmp/bootstrap/cache/packages.php',
        'APP_ROUTES_CACHE' => '/tmp/bootstrap/cache/routes.php',
    ];

    foreach ($envVars as $key => $val) {
        putenv("$key=$val");
        $_ENV[$key] = $val;
        $_SERVER[$key] = $val;
    }

    require __DIR__.'/../vendor/autoload.php';

    /** @var Application $app */
    $app = require __DIR__.'/../bootstrap/app.php';

    // Arahkan storage path instance
    $app->useStoragePath('/tmp/storage');

    // Auto-migrate & seed database on Vercel PHP runtime if needed
    if (! file_exists('/tmp/storage/db_migrated.lock')) {
        try {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
            @file_put_contents('/tmp/storage/db_migrated.lock', date('Y-m-d H:i:s'));
        } catch (Throwable $mE) {
            error_log('Vercel Auto-Migration Note: '.$mE->getMessage());
        }
    }

    // Handle request
    $request = Request::capture();
    $app->handleRequest($request);

} catch (Throwable $e) {
    // Bongkar root cause jika error dibungkus (previous exception)
    $rootException = $e;
    while ($rootException->getPrevious() !== null) {
        $rootException = $rootException->getPrevious();
    }

    error_log('=== VERCEL ROOT ERROR ===');
    error_log(get_class($rootException).': '.$rootException->getMessage().' in '.$rootException->getFile().':'.$rootException->getLine());
    error_log($rootException->getTraceAsString());

    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><title>Deployment Exception</title><style>body{font-family:sans-serif;padding:2rem;background:#fef2f2;color:#991b1b;}pre{background:#fff;padding:1rem;border-radius:8px;overflow-x:auto;border:1px solid #fca5a5;color:#1e293b;}</style></head><body>';
    echo '<h2>Vercel Laravel Deployment Error</h2>';

    echo '<div style="background:#fee2e2;padding:1rem;border-radius:8px;margin-bottom:1rem;border:1px solid #ef4444;">';
    echo '<h3 style="margin-top:0;">Root Cause:</h3>';
    echo '<p><strong>'.htmlspecialchars(get_class($rootException)).': '.htmlspecialchars($rootException->getMessage()).'</strong></p>';
    echo '<p>Location: <code>'.htmlspecialchars($rootException->getFile()).':'.$rootException->getLine().'</code></p>';
    echo '</div>';

    echo '<h3>Full Trace:</h3>';
    echo '<pre>'.htmlspecialchars($rootException->getTraceAsString()).'</pre>';
    echo '</body></html>';
}
