<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'assets';

    protected $fillable = ['name', 'model_number', 'expiry_date', 'price', 'is_fuel_dependent', 'litre_per_unit','is_active'];
}