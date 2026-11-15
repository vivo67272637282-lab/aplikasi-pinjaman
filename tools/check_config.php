<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$config = $app->make('config');
echo "session.driver=" . var_export($config->get('session.driver'), true) . PHP_EOL;
echo "view.paths=" . var_export($config->get('view.paths'), true) . PHP_EOL;
echo "log.default=" . var_export($config->get('logging.default'), true) . PHP_EOL;
