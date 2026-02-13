<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ImportJob;
use App\Jobs\ProcessStockMovementImport;
use App\Jobs\ProcessProductImport;

class ImportRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:run {type} {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run an import job synchronously for testing (type: products|movements)';

    public function handle()
    {
        $type = $this->argument('type');
        $path = $this->argument('path');

        $this->info("Creating import job for {$path} ({$type})");

        $job = ImportJob::create([
            'filename' => basename($path),
            'type' => $type,
            'user_id' => null,
            'status' => 'pending',
            'total_rows' => 0,
            'processed_rows' => 0,
        ]);

        if ($type === 'movements') {
            (new ProcessStockMovementImport($job->id, $path))->handle();
        } elseif ($type === 'products') {
            (new ProcessProductImport($job->id, $path))->handle();
        } else {
            $this->error('Unknown import type: ' . $type);
            return 1;
        }

        $job->refresh();
        $this->info('Job finished with status: ' . $job->status);
        if ($job->error) $this->error('Error: ' . $job->error);
        $this->info("Processed: {$job->processed_rows}/{$job->total_rows}");

        return 0;
    }
}
