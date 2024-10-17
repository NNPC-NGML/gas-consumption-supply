<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GasSituationReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id' => 'integer',
        'customer_site_id' => 'integer',
        'inlet_pressure',
        'outlet_pressure',
        'allocation',
        'nomination'
    ];
    protected $casts = [
        'inlet_pressure' => 'float',
        'outlet_pressure' => 'float',
        'allocation' => 'float',
        'nomination' => 'float',
    ];
}
