<?php

namespace App\Exports;

use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InventoryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Inventory::with('product')->orderBy('inventory_date', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Date Inventaire',
            'Produit',
            'Quantité Constatée',
            'Stock Calculé',
            'Écart',
        ];
    }

    /**
     * @param Inventory $inventory
     * @return array
     */
    public function map($inventory): array
    {
        $calculatedStock = $inventory->product ? $inventory->product->currentStock() : 0;
        $difference = $inventory->quantity - $calculatedStock;

        return [
            $inventory->id,
            $inventory->inventory_date ? $inventory->inventory_date->format('d/m/Y') : '-',
            $inventory->product ? $inventory->product->name : '-',
            $inventory->quantity,
            $calculatedStock,
            $difference,
        ];
    }
}
