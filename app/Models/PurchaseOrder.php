<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'product_id',
        'forecast_id',
        'quantity',
        'status',
        'notes',
        'user_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function forecast()
    {
        return $this->belongsTo(Forecast::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
