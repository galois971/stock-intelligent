<?php

namespace App\Http\Controllers;

use App\Models\StockAlert;

class StockAlertController extends Controller
{
    public function index()
    {
        $alerts = StockAlert::with('product')->where('resolved', false)->get();
        return view('alerts.index', compact('alerts'));
    }

    public function show(StockAlert $alert)
    {
        return view('alerts.show', compact('alert'));
    }

    public function destroy(StockAlert $alert)
    {
        $alert->update(['resolved' => true]);
        return redirect()->route('alerts.index')->with('success', 'Alerte résolue');
    }
}