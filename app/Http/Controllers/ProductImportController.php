<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;
use App\Models\ImportJob;
use App\Jobs\ProcessProductImport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        return view('products.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');

        // store file
        $path = $file->store('imports');

        // create import job
        $job = ImportJob::create([
            'filename' => basename($path),
            'type' => 'products',
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]);

        // dispatch queued job to process import
        ProcessProductImport::dispatch($job->id, $path);

        return redirect()->route('products.import.status', ['importJob' => $job->id]);
    }

    public function statusPage(\App\Models\ImportJob $importJob)
    {
        return view('products.import_status', compact('importJob'));
    }

    public function statusJson(\App\Models\ImportJob $importJob)
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
