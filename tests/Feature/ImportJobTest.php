<?php

namespace Tests\Feature;

use App\Models\ImportJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_job_can_be_created(): void
    {
        $importJob = ImportJob::create([
            'type' => 'product',
            'status' => 'pending',
            'filename' => 'products.csv',
            'total_rows' => 5,
            'processed_rows' => 0,
            'failed_rows' => 0,
            'errors' => [],
        ]);

        $this->assertNotNull($importJob->id);
        $this->assertEquals('pending', $importJob->status);
        $this->assertEquals('product', $importJob->type);
    }

    public function test_import_job_tracks_progress(): void
    {
        $importJob = ImportJob::create([
            'type' => 'product',
            'status' => 'pending',
            'filename' => 'products.csv',
            'total_rows' => 2,
            'processed_rows' => 0,
            'failed_rows' => 0,
            'errors' => [],
        ]);

        $importJob->update([
            'status' => 'processing',
            'processed_rows' => 1,
        ]);

        $this->assertEquals('processing', $importJob->status);
        $this->assertEquals(1, $importJob->processed_rows);
    }

    public function test_import_job_tracks_errors(): void
    {
        $importJob = ImportJob::create([
            'type' => 'product',
            'status' => 'processing',
            'filename' => 'products.csv',
            'total_rows' => 3,
            'processed_rows' => 2,
            'failed_rows' => 1,
            'errors' => [
                ['row' => 3, 'message' => 'Invalid price format'],
            ],
        ]);

        $this->assertEquals(1, $importJob->failed_rows);
        $this->assertCount(1, $importJob->errors);
    }

    public function test_import_job_can_complete(): void
    {
        $importJob = ImportJob::create([
            'type' => 'stock_movement',
            'status' => 'processing',
            'filename' => 'movements.csv',
            'total_rows' => 10,
            'processed_rows' => 10,
            'failed_rows' => 0,
            'errors' => [],
        ]);

        $importJob->update(['status' => 'completed']);

        $this->assertEquals('completed', $importJob->status);
        $this->assertEquals(0, $importJob->failed_rows);
    }

    public function test_import_jobs_queryable_by_status(): void
    {
        ImportJob::create([
            'type' => 'product',
            'status' => 'completed',
            'filename' => 'products_1.csv',
            'total_rows' => 5,
            'processed_rows' => 5,
            'failed_rows' => 0,
            'errors' => [],
        ]);

        ImportJob::create([
            'type' => 'stock_movement',
            'status' => 'processing',
            'filename' => 'movements_1.csv',
            'total_rows' => 10,
            'processed_rows' => 3,
            'failed_rows' => 0,
            'errors' => [],
        ]);

        $completed = ImportJob::where('status', 'completed')->get();
        $processing = ImportJob::where('status', 'processing')->get();

        $this->assertCount(1, $completed);
        $this->assertCount(1, $processing);
    }
}
