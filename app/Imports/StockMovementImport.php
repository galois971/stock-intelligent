<?php

namespace App\Imports;

use App\Models\StockMovement;
use App\Models\Product;
use Carbon\Carbon;

class StockMovementImport
{
    /**
     * Map a row to a StockMovement model
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // accept sku or product_id
        $productId = null;
        if (!empty($row['product_id'])) {
            $productId = intval($row['product_id']);
        } elseif (!empty($row['sku']) || !empty($row['product_sku'])) {
            $sku = $row['sku'] ?? $row['product_sku'];
            $product = Product::where('sku', $sku)->first();
            if ($product) $productId = $product->id;
        } elseif (!empty($row['product']) || !empty($row['product_name'])) {
            $pname = $row['product'] ?? $row['product_name'];
            $product = Product::firstOrCreate(['name' => $pname], ['sku' => null]);
            $productId = $product->id;
        }

        if (!$productId) {
            // cannot map without product
            return null;
        }

        $type = $row['type'] ?? 'entry';
        $subtype = $row['subtype'] ?? null;
        $quantity = isset($row['quantity']) ? intval($row['quantity']) : 0;
        $motif = $row['motif'] ?? null;
        $reference = $row['reference'] ?? null;
        $supplier = $row['supplier'] ?? null;
        $customer = $row['customer'] ?? null;
        $movement_date = null;
        if (!empty($row['movement_date'])) {
            try { $movement_date = Carbon::parse($row['movement_date']); } catch (\Exception $e) { $movement_date = null; }
        }

        $data = [
            'product_id' => $productId,
            'type' => $type,
            'subtype' => $subtype,
            'quantity' => $quantity,
            'motif' => $motif,
            'reference' => $reference,
            'supplier' => $supplier,
            'customer' => $customer,
        ];

        if ($movement_date) $data['movement_date'] = $movement_date;

        // allow passing user_id in the row (job sets it)
        if (!empty($row['user_id'])) $data['user_id'] = intval($row['user_id']);

        return StockMovement::create($data);
    }
}
