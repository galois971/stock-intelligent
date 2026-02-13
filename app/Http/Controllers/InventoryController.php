<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Exports\InventoryExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::with('product')->get();
        return view('inventories.index', compact('inventories'));
    }

    public function create()
    {
        $products = Product::all();
        return view('inventories.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0',
            'inventory_date' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
        ]);

        Inventory::create($request->all());
        return redirect()->route('inventories.index')->with('success', 'Inventaire ajouté avec succès');
    }

    public function show(Inventory $inventory)
    {
        return view('inventories.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        $products = Product::all();
        return view('inventories.edit', compact('inventory', 'products'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0',
            'inventory_date' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
        ]);

        $inventory->update($request->all());
        return redirect()->route('inventories.index')->with('success', 'Inventaire mis à jour avec succès');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventories.index')->with('success', 'Inventaire supprimé avec succès');
    }

    /**
     * Export Excel des inventaires
     */
    public function exportExcel()
    {
        return Excel::download(new InventoryExport, 'inventaires_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export PDF des inventaires
     */
    public function exportPdf()
    {
        $inventories = Inventory::with('product')->orderBy('inventory_date', 'desc')->get();
        $pdf = Pdf::loadView('exports.inventories-pdf', compact('inventories'));
        return $pdf->download('inventaires_' . date('Y-m-d') . '.pdf');
    }
}