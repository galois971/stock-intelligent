<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImportJob;
use App\Jobs\ProcessStockMovementImport;
use Illuminate\Support\Facades\Auth;

class StockMovementImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        return view('movements.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');
        $path = $file->store('imports');

        $job = ImportJob::create([
            'filename' => basename($path),
            'type' => 'movements',
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]);

        ProcessStockMovementImport::dispatch($job->id, $path);

        return redirect()->route('movements.import.status', ['importJob' => $job->id]);
    }

    public function statusPage(ImportJob $importJob)
    {
        return view('products.import_status', compact('importJob'));
    }

    public function statusJson(ImportJob $importJob)
    {
        return response()->json([
            'id' => $importJob->id,
            'status' => $importJob->status,
            'total_rows' => $importJob->total_rows,
            'processed_rows' => $importJob->processed_rows,
            'error' => $importJob->error,
        ]);
    }
}
