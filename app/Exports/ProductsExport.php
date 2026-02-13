<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::with('category')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Code-barres',
            'Catégorie',
            'Prix (€)',
            'Stock Actuel',
            'Stock Minimum',
            'Stock Optimal',
            'Valeur du Stock (€)',
        ];
    }

    /**
     * @param Product $product
     * @return array
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->barcode,
            $product->category ? $product->category->name : '-',
            number_format($product->price, 2, ',', ' '),
            $product->currentStock(),
            $product->stock_min,
            $product->stock_optimal,
            number_format($product->currentStock() * $product->price, 2, ',', ' '),
        ];
    }
}
