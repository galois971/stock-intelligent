<?php
// Script d'aide pour exécuter localement un import stock movement synchronously.
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ImportJob;
use App\Jobs\ProcessStockMovementImport;

// create or use existing file path
$path = 'imports/test_movements.csv';

echo "Creating ImportJob for {$path}\n";
$job = ImportJob::create([
    'filename' => basename($path),
    'type' => 'movements',
    'user_id' => null,
    'status' => 'pending',
    'total_rows' => 0,
    'processed_rows' => 0,
]);

echo "Dispatching job id={$job->id}\n";
(new ProcessStockMovementImport($job->id, $path))->handle();

echo "Job completed. status={$job->fresh()->status}\n";
if ($job->error) echo "Error: {$job->error}\n";
echo "Processed: {$job->fresh()->processed_rows}/{$job->fresh()->total_rows}\n";
