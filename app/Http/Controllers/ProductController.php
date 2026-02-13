<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Exports\ProductsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    // Liste des produits
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    // Formulaire de création
    public function create()
    {
        return view('products.create');
    }

    // Enregistrer un produit
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'barcode' => 'required|unique:products',
            'price' => 'required|numeric|min:0',
            'stock_min' => 'required|numeric|min:0',
            'stock_optimal' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'technical_sheet' => 'nullable|file|mimes:pdf,doc,docx',
        ]);

        $data = $request->all();
        if ($request->hasFile('technical_sheet')) {
            $path = $request->file('technical_sheet')->store('technical_sheets', 'public');
            $data['technical_sheet'] = $path;
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produit ajouté avec succès');
    }

    // Afficher un produit
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    // Formulaire d’édition
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    // Mettre à jour un produit
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'barcode' => 'required|unique:products,barcode,' . $product->id,
            'price' => 'required|numeric|min:0',
            'stock_min' => 'required|numeric|min:0',
            'stock_optimal' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'technical_sheet' => 'nullable|file|mimes:pdf,doc,docx',
        ]);

        $data = $request->all();
        if ($request->hasFile('technical_sheet')) {
            // remove old file if exists
            if ($product->technical_sheet) {
                Storage::disk('public')->delete($product->technical_sheet);
            }
            $path = $request->file('technical_sheet')->store('technical_sheets', 'public');
            $data['technical_sheet'] = $path;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produit mis à jour avec succès');
    }

    // Supprimer un produit
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produit supprimé avec succès');
    }

    /**
     * Export Excel des produits
     */
    public function exportExcel()
    {
        return Excel::download(new ProductsExport, 'produits_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export PDF de la liste des produits
     */
    public function exportPdf()
    {
        $products = Product::with('category')->get();
        $pdf = Pdf::loadView('exports.products-pdf', compact('products'));
        return $pdf->download('produits_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export PDF de la fiche produit
     */
    public function exportProductPdf(Product $product)
    {
        $pdf = Pdf::loadView('exports.product-sheet-pdf', compact('product'));
        return $pdf->download('fiche_produit_' . $product->id . '_' . date('Y-m-d') . '.pdf');
    }
}