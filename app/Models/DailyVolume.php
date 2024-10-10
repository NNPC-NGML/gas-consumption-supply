<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyVolume extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'customer_site_id', 'volume'];

    protected $casts = [
        'customer_id' => 'integer',
        'customer_site_id' => 'integer',
        'volume' => 'float',
    ];
}
