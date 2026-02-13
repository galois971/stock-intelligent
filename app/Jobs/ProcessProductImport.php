<?php

namespace App\Jobs;

use App\Models\ImportJob;
use App\Imports\ProductImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Product;
use Illuminate\Filesystem\Filesystem;

class ProcessProductImport implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $importJobId;
    protected $path;

    public function __construct(int $importJobId, string $path)
    {
        $this->importJobId = $importJobId;
        $this->path = $path;
    }

    public function handle()
    {
        $job = ImportJob::find($this->importJobId);
        if (!$job) return;

        $job->status = 'processing';
        $job->save();

        try {
            // Read all rows (works for moderate files). For very large files, consider chunk reading.
            $rows = Excel::toArray(new ProductImport(), storage_path('app/' . $this->path));
            $sheet = $rows[0] ?? [];
            $total = count($sheet);
            $job->total_rows = $total;
            $job->processed_rows = 0;
            $job->save();

            $fs = new Filesystem();

            foreach ($sheet as $index => $row) {
                // Ensure header row handling: if row is associative (heading row), ProductImport handles mapping.
                // We'll reuse ProductImport->model logic by constructing an array with lowercase keys matching headings.
                // If the sheet has numeric keys, skip if empty.
                if (empty(array_filter((array) $row))) {
                    $job->processed_rows++;
                    if ($job->processed_rows % 20 == 0) $job->save();
                    continue;
                }

                // Normalize keys to expected headings if possible
                $norm = [];
                foreach ($row as $k => $v) {
                    if (is_string($k)) $norm[strtolower($k)] = $v; else $norm[] = $v;
                }

                // Reuse ProductImport logic by directly calling model()
                $import = new ProductImport();
                try {
                    $model = $import->model($norm);
                } catch (\Exception $e) {
                    // ignore row errors but log in job error note
                    $job->error = trim(($job->error ? $job->error . "\n" : '') . "Row {$index} error: " . $e->getMessage());
                }

                $job->processed_rows++;
                if ($job->processed_rows % 20 == 0) $job->save();
            }

            $job->status = 'completed';
            $job->save();
        } catch (\Exception $e) {
            $job->status = 'failed';
            $job->error = $e->getMessage();
            $job->save();
        }
    }
}
