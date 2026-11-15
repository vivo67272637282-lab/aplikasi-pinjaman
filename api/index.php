<?php

define('LARAVEL_START', microtime(true));

$projectRoot = dirname(__DIR__);
chdir($projectRoot);

// Buat semua direktori yang dibutuhkan Laravel di /tmp (satu-satunya folder writable di Vercel)
$tmpBase = '/tmp/laravel';
foreach ([
    $tmpBase . '/framework/cache/data',
    $tmpBase . '/framework/views',
    $tmpBase . '/framework/sessions',
    $tmpBase . '/framework/testing',
    $tmpBase . '/logs',
    $tmpBase . '/app/public',
] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

require $projectRoot . '/vendor/autoload.php';

$app = require_once $projectRoot . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
