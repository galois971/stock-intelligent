<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockAlert;
use App\Models\User;
use App\Notifications\StockAlertNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ExpirationAlertService
{
    private array $alertedProductIds = [];

    /**
     * Vérifier les expirations et créer les alertes
     */
    public function checkAndGenerateExpirationAlerts()
    {
        // Note: Ne PAS réinitialiser $alertedProductIds pour eviter les doublons entre appels
        
        // Récupérer les produits qui expirent bientôt (dans les 30 jours)
        $expiringProducts = Product::expiringWithin(30)->get();

        foreach ($expiringProducts as $product) {
            $this->createExpirationAlert($product);
        }

        // Récupérer les produits expirés
        $expiredProducts = Product::expired()->get();

        foreach ($expiredProducts as $product) {
            $this->createExpiredAlert($product);
        }
    }

    /**
     * Créer une alerte pour produit expirant bientôt
     * Retourne vrai si une nouvelle alerte a été créée
     */
    private function createExpirationAlert(Product $product): bool
    {
        // Vérifier qu'on ne traite pas ce produit deux fois dans la même exécution
        // (évite les doublons même si la méthode est appelée plusieurs fois)
        if (in_array($product->id, $this->alertedProductIds)) {
            return false;
        }

        // Vérifier s'il existe une alerte non résolue pour ce produit qui n'est pas "EXPIRÉ"
        $alerts = StockAlert::where('product_id', $product->id)
            ->where('alert_type', 'expiration')
            ->where('is_resolved', false)
            ->get();

        // Chercher une alerte qui n'est PAS "EXPIRÉ"
        foreach ($alerts as $alert) {
            if (strpos($alert->message, 'EXPIRÉ') === false) {
                // Une alerte "expiring" existe déjà
                $this->alertedProductIds[] = $product->id;
                return false;
            }
        }

        $daysLeft = $product->daysUntilExpiration();
        $expirationDate = $product->expiration_date instanceof Carbon ? 
            $product->expiration_date : 
            Carbon::parse($product->expiration_date);

        $alert = StockAlert::create([
            'product_id' => $product->id,
            'alert_type' => 'expiration',
            'current_quantity' => $product->currentStock(),
            'message' => "{$product->name} expire le {$expirationDate->format('d/m/Y')} ({$daysLeft} jours restants)",
            'is_resolved' => false,
        ]);
        
        $this->alertedProductIds[] = $product->id;
        $this->sendExpirationNotification($alert);
        return true;
    }

    /**
     * Créer une alerte pour produit expiré
     * Retourne vrai si une nouvelle alerte a été créée
     */
    private function createExpiredAlert(Product $product): bool
    {
        // Vérifier qu'on ne traite pas ce produit deux fois dans la même exécution
        if (in_array($product->id, $this->alertedProductIds)) {
            return false;
        }

        // Vérifier s'il existe une alerte non résolue pour ce produit qui dit "EXPIRÉ"
        $alerts = StockAlert::where('product_id', $product->id)
            ->where('alert_type', 'expiration')
            ->where('is_resolved', false)
            ->get();

        // Chercher une alerte qui contient "EXPIRÉ"
        foreach ($alerts as $alert) {
            if (strpos($alert->message, 'EXPIRÉ') !== false) {
                // Une alerte "expired" existe déjà
                $this->alertedProductIds[] = $product->id;
                return false;
            }
        }

        $expirationDate = $product->expiration_date instanceof Carbon ? 
            $product->expiration_date : 
            Carbon::parse($product->expiration_date);

        $alert = StockAlert::create([
            'product_id' => $product->id,
            'alert_type' => 'expiration',
            'current_quantity' => $product->currentStock(),
            'message' => "{$product->name} est EXPIRÉ depuis le {$expirationDate->format('d/m/Y')}. Action requise : retirer du stock.",
            'is_resolved' => false,
        ]);

        $this->alertedProductIds[] = $product->id;
        $this->sendExpirationNotification($alert);
        return true;
    }

    /**
     * Envoyer les notifications pour alerte expiration
     */
    private function sendExpirationNotification(StockAlert $alert)
    {
        // Récupérer tous les administrateurs et gestionnaires
        $users = User::role(['admin', 'gestionnaire'])->get();

        if ($users->count() > 0) {
            Notification::send($users, new StockAlertNotification($alert));
        }
    }
}
