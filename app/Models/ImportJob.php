<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ImportJob extends Model
{
    use HasFactory;
    protected $fillable = [
        'filename', 'type', 'user_id', 'total_rows', 'processed_rows', 'status', 'error'
    ];
}
