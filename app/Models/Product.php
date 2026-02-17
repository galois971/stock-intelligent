<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'barcode',
        'price',
        'stock_min',
        'stock_optimal',
        'category_id',
        'technical_sheet',
        'expiration_date',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Calculate current stock based on movements and latest inventory.
     */
    public function currentStock(): int
    {
        $entries = $this->movements()->where('type', 'entry')->sum('quantity') ?: 0;
        $exits = $this->movements()->where('type', 'exit')->sum('quantity') ?: 0;

        // Prefer the latest inventory if present
        $latestInventory = $this->inventories()->orderByDesc('inventory_date')->first();
        $inventoryQty = $latestInventory ? $latestInventory->quantity : 0;

        return $inventoryQty + ($entries - $exits);
    }

    /**
     * Vérifier si le produit a expiré
     */
    public function isExpired(): bool
    {
        if (!$this->expiration_date) {
            return false;
        }

        return Carbon::now()->isAfter($this->expiration_date);
    }

    /**
     * Vérifier si le produit expire bientôt (dans les N jours)
     */
    public function isExpiringWithin(int $days = 30): bool
    {
        if (!$this->expiration_date) {
            return false;
        }

        $today = Carbon::now();
        $expirationDate = $this->expiration_date;

        // Le produit expire dans le futur ET dans les N jours spécifiés
        return $expirationDate->isAfter($today) && 
               $today->diffInDays($expirationDate) <= $days;
    }

    /**
     * Obtenir les jours avant expiration
     */
    public function daysUntilExpiration(): ?int
    {
        if (!$this->expiration_date) {
            return null;
        }

        return Carbon::now()->diffInDays($this->expiration_date);
    }

    /**
     * Scope: Produits expirés
     */
    public function scopeExpired($query)
    {
        return $query->whereDate('expiration_date', '<', Carbon::now());
    }

    /**
     * Scope: Produits expirant dans les N jours
     */
    public function scopeExpiringWithin($query, int $days = 30)
    {
        return $query->whereDate('expiration_date', '<=', Carbon::now()->addDays($days))
                     ->whereDate('expiration_date', '>', Carbon::now());
    }

    /**
     * Scope: Produits sans date d'expiration
     */
    public function scopeWithoutExpiration($query)
    {
        return $query->whereNull('expiration_date');
    }

    /**
     * Scope: Produits avec date d'expiration
     */
    public function scopeWithExpiration($query)
    {
        return $query->whereNotNull('expiration_date');
    }
}

