<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportJob extends Model
{
    protected $fillable = [
        'filename', 'type', 'user_id', 'total_rows', 'processed_rows', 'status', 'error'
    ];
}
