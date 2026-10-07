<?php

// Display errors for debugging on serverless
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
    // Ensure /tmp directories exist for serverless Vercel environment
    $tmpDirs = [
        '/tmp/storage/app/public',
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs',
        '/tmp/bootstrap/cache',
        '/tmp/database',
    ];

    foreach ($tmpDirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }

    // Copy database.sqlite to /tmp if present
    $dbSource = __DIR__ . '/../database/database.sqlite';
    $dbDest = '/tmp/database/database.sqlite';
    if (file_exists($dbSource) && (!file_exists($dbDest) || filesize($dbDest) === 0)) {
        @copy($dbSource, $dbDest);
    }

    // Set runtime env vars for Serverless Lambda environment
    $_ENV['APP_STORAGE'] = '/tmp/storage';
    $_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
    $_ENV['LOG_CHANNEL'] = 'stderr';
    $_ENV['CACHE_STORE'] = 'array';
    $_ENV['CACHE_DRIVER'] = 'array';
    $_ENV['SESSION_DRIVER'] = 'cookie';

    putenv('APP_STORAGE=/tmp/storage');
    putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
    putenv('LOG_CHANNEL=stderr');
    putenv('CACHE_STORE=array');
    putenv('CACHE_DRIVER=array');
    putenv('SESSION_DRIVER=cookie');

    if (file_exists($dbDest)) {
        $_ENV['DB_DATABASE'] = $dbDest;
        putenv("DB_DATABASE={$dbDest}");
    }

    // Default APP_KEY if not configured in Vercel environment variables
    if (empty($_ENV['APP_KEY']) && empty(getenv('APP_KEY'))) {
        $fallbackKey = 'base64:M1gDuKM6omBJc68Cikycsz0nDE0u+AbyFgbhAZSEPLg=';
        $_ENV['APP_KEY'] = $fallbackKey;
        putenv("APP_KEY={$fallbackKey}");
    }

    // Forward to Laravel public/index.php
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><title>Application Error</title></head><body style="font-family: sans-serif; padding: 2rem;">';
    echo '<h2>Serverless Application Error</h2>';
    echo '<p style="color: red;"><strong>' . htmlspecialchars($e->getMessage()) . '</strong></p>';
    echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ' (Line ' . $e->getLine() . ')</p>';
    echo '<pre style="background: #f4f4f4; padding: 1rem; border-radius: 4px; overflow-x: auto;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</body></html>';
}
