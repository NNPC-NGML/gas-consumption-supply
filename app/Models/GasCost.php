<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GasCost extends Model
{
    use HasFactory;
    protected $fillable = [
        'date_of_entry',
        'dollar_cost_per_scf',
        'dollar_rate',
        'status'
    ];
    protected $casts = [
        'date_of_entry' => 'date',
        'status' => 'boolean'
    ];
}
