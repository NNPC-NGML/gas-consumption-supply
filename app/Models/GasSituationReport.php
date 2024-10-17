<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Skillz\Nnpcreusable\Models\Customer;
use Skillz\Nnpcreusable\Models\CustomerSite;

class GasSituationReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'customer_site_id',
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
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function customer_site()
    {
        return $this->belongsTo(CustomerSite::class);
    }
}
