<?php

/**
 * Vercel PHP Runtime entry point for Laravel.
 * Routes all requests through Laravel's public/index.php
 */

// Set the working directory to the project root
$projectRoot = dirname(__DIR__);

// Change to project root so Laravel can find its files
chdir($projectRoot);

// Bootstrap Laravel through the public entry point
define('LARAVEL_START', microtime(true));

require $projectRoot . '/vendor/autoload.php';

$app = require_once $projectRoot . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
