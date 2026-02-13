<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    protected $fillable = [
        'product_id',
        'alert_type',
        'current_quantity',
        'message',
        'resolved',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
