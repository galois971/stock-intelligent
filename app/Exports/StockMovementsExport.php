<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StockMovementsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = StockMovement::with(['product', 'user']);

        if ($this->startDate) {
            $query->where('movement_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('movement_date', '<=', $this->endDate);
        }

        return $query->orderBy('movement_date', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Produit',
            'Type',
            'Sous-type',
            'Quantité',
            'Utilisateur',
            'Motif',
            'Référence',
            'Fournisseur/Client',
        ];
    }

    /**
     * @param StockMovement $movement
     * @return array
     */
    public function map($movement): array
    {
        return [
            $movement->id,
            $movement->movement_date ? $movement->movement_date->format('d/m/Y H:i') : '-',
            $movement->product ? $movement->product->name : '-',
            $movement->type === 'entry' ? 'Entrée' : 'Sortie',
            $movement->subtype_label ?? '-',
            $movement->quantity,
            $movement->user ? $movement->user->name : '-',
            $movement->motif ?? '-',
            $movement->reference ?? '-',
            $movement->supplier ?? $movement->customer ?? '-',
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
