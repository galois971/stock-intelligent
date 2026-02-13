<?php

namespace App\Observers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\StockAlert;
use App\Models\User;
use App\Notifications\StockAlertNotification;
use Illuminate\Support\Facades\Notification;

class StockMovementObserver
{
    /**
     * Méthode déclenchée quand un mouvement est créé
     */
    public function created(StockMovement $movement)
    {
        $product = Product::find($movement->product_id);

        if ($product) {
            // Calculer la quantité actuelle
            $entries = StockMovement::where('product_id', $product->id)
                ->where('type', 'entry')
                ->sum('quantity');

            $exits = StockMovement::where('product_id', $product->id)
                ->where('type', 'exit')
                ->sum('quantity');

            $currentQuantity = $entries - $exits;

            // Vérifier si une alerte similaire existe déjà (non résolue)
            $existingAlert = StockAlert::where('product_id', $product->id)
                ->where('alert_type', 'low_stock')
                ->where('resolved', false)
                ->first();

            // Vérifier les seuils - Stock minimum atteint
            if ($currentQuantity < $product->stock_min && !$existingAlert) {
                $alert = StockAlert::create([
                    'product_id' => $product->id,
                    'alert_type' => 'low_stock',
                    'current_quantity' => $currentQuantity,
                    'message' => "Stock trop bas pour {$product->name}. Stock actuel : {$currentQuantity}, minimum requis : {$product->stock_min}",
                ]);

                // Envoyer l'email aux administrateurs et gestionnaires
                $this->sendAlertNotification($alert);
            }

            // Vérifier le risque de rupture (stock < stock_min * 1.2)
            $riskThreshold = $product->stock_min * 1.2;
            $existingRiskAlert = StockAlert::where('product_id', $product->id)
                ->where('alert_type', 'risk_of_rupture')
                ->where('resolved', false)
                ->first();

            if ($currentQuantity < $riskThreshold && $currentQuantity >= $product->stock_min && !$existingRiskAlert) {
                $alert = StockAlert::create([
                    'product_id' => $product->id,
                    'alert_type' => 'risk_of_rupture',
                    'current_quantity' => $currentQuantity,
                    'message' => "Risque de rupture pour {$product->name}. Stock actuel : {$currentQuantity}",
                ]);

                $this->sendAlertNotification($alert);
            }

            // Vérifier le surstock
            $existingOverstockAlert = StockAlert::where('product_id', $product->id)
                ->where('alert_type', 'overstock')
                ->where('resolved', false)
                ->first();

            if ($currentQuantity > $product->stock_optimal && !$existingOverstockAlert) {
                $alert = StockAlert::create([
                    'product_id' => $product->id,
                    'alert_type' => 'overstock',
                    'current_quantity' => $currentQuantity,
                    'message' => "Stock trop élevé pour {$product->name}. Stock actuel : {$currentQuantity}, optimal : {$product->stock_optimal}",
                ]);

                $this->sendAlertNotification($alert);
            }

            // Si le mouvement indique une expiration, générer une alerte d'expiration
            if ($movement->subtype === 'expiration') {
                $existingExpiration = StockAlert::where('product_id', $product->id)
                    ->where('alert_type', 'expiration')
                    ->where('resolved', false)
                    ->first();

                if (! $existingExpiration) {
                    $alert = StockAlert::create([
                        'product_id' => $product->id,
                        'alert_type' => 'expiration',
                        'current_quantity' => $currentQuantity,
                        'message' => "Expiration détectée pour {$product->name}. Quantité affectée : {$movement->quantity}",
                    ]);

                    $this->sendAlertNotification($alert);
                }
            }
        }
    }

    /**
     * Envoie les notifications par email aux administrateurs et gestionnaires
     */
    protected function sendAlertNotification(StockAlert $alert)
    {
        // Récupérer tous les administrateurs et gestionnaires
        $users = User::role(['admin', 'gestionnaire'])->get();

        if ($users->count() > 0) {
            Notification::send($users, new StockAlertNotification($alert));
        }
    }

    /**
     * Handle the StockMovement "updated" event.
     */
    public function updated(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "deleted" event.
     */
    public function deleted(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "restored" event.
     */
    public function restored(StockMovement $stockMovement): void
    {
        //
    }

    /**
     * Handle the StockMovement "force deleted" event.
     */
    public function forceDeleted(StockMovement $stockMovement): void
    {
        //
    }
}
