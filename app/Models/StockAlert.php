<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockAlert extends Model
{
    use HasFactory;
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
