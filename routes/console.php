<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Product;
use App\Models\StockAlert;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StockAlertNotification;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Scan products and generate stock alerts when thresholds are crossed.
 * Usage: php artisan alerts:scan
 */
Artisan::command('alerts:scan', function () {
    $this->info('Scanning products for stock alerts...');

    $products = Product::with('inventories', 'movements')->get();

    /** @var Product $product */
    foreach ($products as $product) {
        $current = (int) $product->currentStock();

        // low stock
        $existing = StockAlert::where('product_id', $product->id)->where('alert_type', 'low_stock')->where('resolved', false)->first();
        if ($current < $product->stock_min && ! $existing) {
            $alert = StockAlert::create([
                'product_id' => $product->id,
                'alert_type' => 'low_stock',
                'current_quantity' => $current,
                'message' => "Stock trop bas pour {$product->name}. Stock actuel : {$current}, minimum requis : {$product->stock_min}",
            ]);

            // notify
            $users = \App\Models\User::role(['admin', 'gestionnaire'])->get();
            if ($users->count()) {
                Notification::send($users, new StockAlertNotification($alert));
            }
        }

        // risk of rupture
        $riskThreshold = $product->stock_min * 1.2;
        $existingRisk = StockAlert::where('product_id', $product->id)->where('alert_type', 'risk_of_rupture')->where('resolved', false)->first();
        if ($current < $riskThreshold && $current >= $product->stock_min && ! $existingRisk) {
            $alert = StockAlert::create([
                'product_id' => $product->id,
                'alert_type' => 'risk_of_rupture',
                'current_quantity' => $current,
                'message' => "Risque de rupture pour {$product->name}. Stock actuel : {$current}",
            ]);

            $users = \App\Models\User::role(['admin', 'gestionnaire'])->get();
            if ($users->count()) {
                Notification::send($users, new StockAlertNotification($alert));
            }
        }

        // overstock
        $existingOver = StockAlert::where('product_id', $product->id)->where('alert_type', 'overstock')->where('resolved', false)->first();
        if ($current > $product->stock_optimal && ! $existingOver) {
            $alert = StockAlert::create([
                'product_id' => $product->id,
                'alert_type' => 'overstock',
                'current_quantity' => $current,
                'message' => "Stock trop élevé pour {$product->name}. Stock actuel : {$current}, optimal : {$product->stock_optimal}",
            ]);

            $users = \App\Models\User::role(['admin', 'gestionnaire'])->get();
            if ($users->count()) {
                Notification::send($users, new StockAlertNotification($alert));
            }
        }
    }

    $this->info('Scan complete.');
})->describe('Scan all products and create stock alerts when needed');
