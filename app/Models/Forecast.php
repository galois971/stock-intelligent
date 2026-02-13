<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forecast extends Model
{
    protected $fillable = [
        'product_id',
        'method',
        'history_days',
        'forecast_days',
        'history',
        'forecast',
        'rmse',
        'mape',
    ];

    protected $casts = [
        'history' => 'array',
        'forecast' => 'array',
        'rmse' => 'float',
        'mape' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
