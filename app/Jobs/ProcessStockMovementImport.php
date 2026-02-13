<?php

namespace App\Jobs;

use App\Models\ImportJob;
use App\Imports\StockMovementImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ProcessStockMovementImport implements ShouldQueue
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
            $fullPath = storage_path('app/' . $this->path);

            if (!file_exists($fullPath)) {
                throw new \Exception('Import file not found: ' . $fullPath);
            }

            $rows = [];
            $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

            if ($ext === 'csv') {
                if (($handle = fopen($fullPath, 'r')) !== false) {
                    $header = null;
                    while (($data = fgetcsv($handle, 0, ',')) !== false) {
                        if (!$header) {
                            $header = array_map(function ($h) { return strtolower(trim($h)); }, $data);
                            continue;
                        }
                        $rows[] = array_combine($header, $data);
                    }
                    fclose($handle);
                }
            } else {
                // Try to read using the older maatwebsite excel load API if available
                if (class_exists('\Maatwebsite\\Excel\\Excel')) {
                    $excel = app('excel');
                    $reader = $excel->load($fullPath);
                    $sheet = $reader->get();
                    foreach ($sheet as $r) {
                        $rows[] = array_change_key_case((array) $r, CASE_LOWER);
                    }
                } else {
                    throw new \Exception('Unsupported file type for import: ' . $ext);
                }
            }

            $total = count($rows);
            $job->total_rows = $total;
            $job->processed_rows = 0;
            $job->save();

            foreach ($rows as $index => $row) {
                if (empty(array_filter((array) $row))) {
                    $job->processed_rows++;
                    if ($job->processed_rows % 20 == 0) $job->save();
                    continue;
                }

                // attach importing user for audit
                if ($job->user_id) $row['user_id'] = $job->user_id;

                $import = new StockMovementImport();
                try {
                    $import->model($row);
                } catch (\Exception $e) {
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
