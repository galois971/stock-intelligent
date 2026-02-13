<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Exports\StockMovementsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class StockMovementController extends Controller
{
    public function index()
    {
        $movements = StockMovement::with(['product', 'user'])->orderBy('movement_date', 'desc')->get();
        return view('movements.index', compact('movements'));
    }

    public function create()
    {
        $products = Product::all();
        return view('movements.create', compact('products'));
    }

    public function store(Request $request)
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:entry,exit',
            'quantity' => 'required|numeric|min:1',
            'motif' => 'nullable|string|max:500',
            'movement_date' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'customer' => 'nullable|string|max:255',
        ];

        // Validation du subtype selon le type
        if ($request->type === 'entry') {
            $rules['subtype'] = 'nullable|in:achat,retour,correction';
        } else {
            $rules['subtype'] = 'nullable|in:vente,perte,casse,expiration';
        }

        $request->validate($rules);

        $data = $request->all();
        // Assigner automatiquement l'utilisateur connecté
        $data['user_id'] = auth()->id();

        StockMovement::create($data);
        return redirect()->route('movements.index')->with('success', 'Mouvement enregistré avec succès');
    }

    public function show(StockMovement $movement)
    {
        return view('movements.show', compact('movement'));
    }

    public function edit(StockMovement $movement)
    {
        $products = Product::all();
        return view('movements.edit', compact('movement', 'products'));
    }

    public function update(Request $request, StockMovement $movement)
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:entry,exit',
            'quantity' => 'required|numeric|min:1',
            'motif' => 'nullable|string|max:500',
            'movement_date' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'customer' => 'nullable|string|max:255',
        ];

        // Validation du subtype selon le type
        if ($request->type === 'entry') {
            $rules['subtype'] = 'nullable|in:achat,retour,correction';
        } else {
            $rules['subtype'] = 'nullable|in:vente,perte,casse,expiration';
        }

        $request->validate($rules);

        $movement->update($request->all());
        return redirect()->route('movements.index')->with('success', 'Mouvement mis à jour avec succès');
    }

    public function destroy(StockMovement $movement)
    {
        $movement->delete();
        return redirect()->route('movements.index')->with('success', 'Mouvement supprimé avec succès');
    }

    /**
     * Export Excel des mouvements
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        return Excel::download(
            new StockMovementsExport($startDate, $endDate),
            'mouvements_' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export PDF des mouvements
     */
    public function exportPdf(Request $request)
    {
        $query = StockMovement::with(['product', 'user']);
        
        if ($request->start_date) {
            $query->where('movement_date', '>=', $request->start_date);
        }
        
        if ($request->end_date) {
            $query->where('movement_date', '<=', $request->end_date);
        }
        
        $movements = $query->orderBy('movement_date', 'desc')->get();
        $pdf = Pdf::loadView('exports.movements-pdf', compact('movements'));
        return $pdf->download('mouvements_' . date('Y-m-d') . '.pdf');
    }
}