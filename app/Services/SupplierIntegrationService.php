<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Http;

class SupplierIntegrationService
{
    /**
     * Send a purchase order to the external supplier endpoint.
     * Returns array with keys: success(bool), status(int), body(mixed)
     */
    public function sendOrder(PurchaseOrder $po): array
    {
        $endpoint = env('SUPPLIER_ENDPOINT');
        if (empty($endpoint)) {
            return ['success' => false, 'status' => 0, 'body' => 'No supplier endpoint configured'];
        }

        try {
            $payload = [
                'purchase_order_id' => $po->id,
                'product_id' => $po->product_id,
                'quantity' => $po->quantity,
                'forecast_id' => $po->forecast_id,
                'notes' => $po->notes,
            ];

            $res = Http::timeout(10)->post($endpoint, $payload);

            return ['success' => $res->successful(), 'status' => $res->status(), 'body' => $res->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'status' => 0, 'body' => $e->getMessage()];
        }
    }
}
