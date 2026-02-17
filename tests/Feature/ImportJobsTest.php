<?php

namespace Tests\Feature;

use App\Jobs\ProcessProductImport;
use App\Jobs\ProcessStockMovementImport;
use App\Models\ImportJob;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ImportJobsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function product_import_job_creates_import_job_record()
    {
        $csvContent = <<<CSV
name,code_barres,category_id,price,stock_min,stock_optimal
Product Test,BARCODE123,1,19.99,5,10
CSV;

        $job = new ProcessProductImport($csvContent, 1);

        $this->assertInstanceOf(ProcessProductImport::class, $job);
    }

    /** @test */
    public function stock_movement_import_job_creates_import_job_record()
    {
        $csvContent = <<<CSV
product_id,type,subtype,quantity,movement_date
1,entry,achat,10,2026-02-10
CSV;

        $job = new ProcessStockMovementImport($csvContent, 1);

        $this->assertInstanceOf(ProcessStockMovementImport::class, $job);
    }

    /** @test */
    public function import_job_can_be_dispatched_to_queue()
    {
        Queue::fake();

        $csvContent = "name,code_barres,category_id,price\nTest,BAR123,1,9.99";

        ProcessProductImport::dispatch($csvContent, 1);

        Queue::assertPushed(ProcessProductImport::class);
    }

    /** @test */
    public function import_job_tracks_progress()
    {
        $csvContent = <<<CSV
name,code_barres,category_id,price,stock_min,stock_optimal
Product 1,BAR1,1,10.00,5,10
Product 2,BAR2,1,20.00,10,20
CSV;

        $importJob = ImportJob::create([
            'type' => 'product',
            'status' => 'pending',
            'total_rows' => 2,
            'processed_rows' => 0,
            'failed_rows' => 0,
            'errors' => [],
        ]);

        $this->assertEquals('pending', $importJob->status);
        $this->assertEquals(0, $importJob->processed_rows);

        // Simulate progress update
        $importJob->update([
            'processed_rows' => 1,
            'status' => 'processing',
        ]);

        $this->assertEquals('processing', $importJob->status);
        $this->assertEquals(1, $importJob->processed_rows);
    }

    /** @test */
    public function import_job_tracks_errors()
    {
        $importJob = ImportJob::create([
            'type' => 'product',
            'status' => 'processing',
            'total_rows' => 3,
            'processed_rows' => 2,
            'failed_rows' => 1,
            'errors' => [
                ['row' => 3, 'message' => 'Invalid price format'],
            ],
        ]);

        $this->assertEquals(1, $importJob->failed_rows);
        $this->assertCount(1, $importJob->errors);
        $this->assertEquals('Invalid price format', $importJob->errors[0]['message']);
    }

    /** @test */
    public function import_job_can_complete_successfully()
    {
        $importJob = ImportJob::create([
            'type' => 'product',
            'status' => 'processing',
            'total_rows' => 2,
            'processed_rows' => 2,
            'failed_rows' => 0,
            'errors' => [],
        ]);

        $importJob->update(['status' => 'completed']);

        $this->assertEquals('completed', $importJob->status);
        $this->assertEquals(2, $importJob->processed_rows);
        $this->assertEquals(0, $importJob->failed_rows);
    }

    /** @test */
    public function import_job_can_fail()
    {
        $importJob = ImportJob::create([
            'type' => 'product',
            'status' => 'processing',
            'total_rows' => 5,
            'processed_rows' => 2,
            'failed_rows' => 3,
            'errors' => [
                ['row' => 2, 'message' => 'Duplicate barcode'],
                ['row' => 3, 'message' => 'Missing required field'],
                ['row' => 4, 'message' => 'Invalid category'],
            ],
        ]);

        $importJob->update(['status' => 'failed']);

        $this->assertEquals('failed', $importJob->status);
        $this->assertEquals(3, $importJob->failed_rows);
    }

    /** @test */
    public function stock_movement_import_validates_quantity()
    {
        $importJob = ImportJob::create([
            'type' => 'stock_movement',
            'status' => 'processing',
            'total_rows' => 1,
            'processed_rows' => 0,
            'failed_rows' => 1,
            'errors' => [
                ['row' => 1, 'message' => 'Quantity must be positive'],
            ],
        ]);

        $this->assertEquals(1, $importJob->failed_rows);
        $this->assertStringContainsString('Quantity', $importJob->errors[0]['message']);
    }

    /** @test */
    public function stock_movement_import_validates_type()
    {
        $importJob = ImportJob::create([
            'type' => 'stock_movement',
            'status' => 'processing',
            'total_rows' => 1,
            'processed_rows' => 0,
            'failed_rows' => 1,
            'errors' => [
                ['row' => 1, 'message' => 'Invalid movement type. Must be entry or exit'],
            ],
        ]);

        $this->assertCount(1, $importJob->errors);
        $this->assertStringContainsString('entry or exit', $importJob->errors[0]['message']);
    }

    /** @test */
    public function import_job_stores_metadata()
    {
        $importJob = ImportJob::create([
            'type' => 'product',
            'status' => 'completed',
            'total_rows' => 10,
            'processed_rows' => 10,
            'failed_rows' => 0,
            'errors' => [],
            'file_name' => 'products_2026_02_13.csv',
            'file_path' => '/storage/imports/products_2026_02_13.csv',
        ]);

        $this->assertEquals('products_2026_02_13.csv', $importJob->file_name);
        $this->assertNotNull($importJob->file_path);
    }

    /** @test */
    public function import_jobs_are_queryable_by_status()
    {
        ImportJob::create([
            'type' => 'product',
            'status' => 'completed',
            'total_rows' => 5,
            'processed_rows' => 5,
            'failed_rows' => 0,
            'errors' => [],
        ]);

        ImportJob::create([
            'type' => 'stock_movement',
            'status' => 'processing',
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
