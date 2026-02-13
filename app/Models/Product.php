<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'barcode',
        'price',
        'stock_min',
        'stock_optimal',
        'category_id',
        'technical_sheet',
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
}
