<?php

define('LARAVEL_START', microtime(true));

$projectRoot = dirname(__DIR__);
chdir($projectRoot);

// Vercel: /tmp adalah satu-satunya folder writable
// Setup semua direktori yang dibutuhkan Laravel sebelum bootstrap
$tmpBase = '/tmp/laravel';
$dirs = [
    $tmpBase . '/framework/cache/data',
    $tmpBase . '/framework/views',
    $tmpBase . '/framework/sessions',
    $tmpBase . '/framework/testing',
    $tmpBase . '/logs',
    $tmpBase . '/app/public',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Override storage path ke /tmp sebelum Laravel boot
$_ENV['APP_STORAGE_PATH'] = $tmpBase;

require $projectRoot . '/vendor/autoload.php';

// Patch application storage path sebelum instance dibuat
$app = require_once $projectRoot . '/bootstrap/app.php';

// Override storage path langsung di app instance
$app->useStoragePath($tmpBase);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
