<?php
// Usage:
// php tools/import_json.php data/file.json table_name tools/mappings/example_mapping.php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$argv0 = $argv[0] ?? 'import_json.php';
if (!isset($argv[1]) || !isset($argv[2])) {
    echo "Usage: php {$argv0} path/to/file.json table_name [mapping_php_or_json]\n";
    echo "mapping can be a PHP file that returns an array or a JSON string mapping json_key=>column_name\n";
    exit(1);
}

$jsonPath = $argv[1];
$table = $argv[2];
$mappingArg = $argv[3] ?? null;

if (!file_exists($jsonPath)) {
    echo "JSON file not found: {$jsonPath}\n";
    exit(1);
}

$mapping = [];
if ($mappingArg) {
    if (file_exists($mappingArg)) {
        $mapping = include $mappingArg;
        if (!is_array($mapping)) {
            echo "Mapping file must return an array.\n";
            exit(1);
        }
    } else {
        $decoded = json_decode($mappingArg, true);
        if (is_array($decoded)) {
            $mapping = $decoded;
        } else {
            echo "Mapping not recognized. Provide existing PHP file or JSON mapping string.\n";
            exit(1);
        }
    }
}

$payload = json_decode(file_get_contents($jsonPath), true);
if ($payload === null) {
    echo "Failed to parse JSON file: " . json_last_error_msg() . "\n";
    exit(1);
}

// If top-level is associative (single object), convert to array
if (array_keys($payload) !== range(0, count($payload) - 1)) {
    // not a sequential array, wrap it
    $payload = [$payload];
}

$total = 0;
$chunkSize = 200;

try {
    DB::beginTransaction();
    foreach (array_chunk($payload, $chunkSize) as $chunk) {
        $inserts = [];
        foreach ($chunk as $item) {
            $row = [];
            if (!empty($mapping)) {
                foreach ($mapping as $jsonKey => $colName) {
                    // support dot notation in jsonKey
                    $value = data_get($item, $jsonKey);
                    $row[$colName] = $value;
                }
            } else {
                // no mapping: assume JSON keys == column names
                if (!is_array($item)) {
                    continue;
                }
                $row = $item;
            }
            $inserts[] = $row;
        }

        if (!empty($inserts)) {
            DB::table($table)->insert($inserts);
            $total += count($inserts);
            echo "Inserted: " . count($inserts) . " rows...\n";
        }
    }
    DB::commit();
    echo "Import complete. Total inserted: {$total}\n";
} catch (Throwable $e) {
    DB::rollBack();
    echo "Import failed: " . $e->getMessage() . "\n";
    exit(1);
}
