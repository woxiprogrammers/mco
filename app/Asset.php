<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Asset extends Model
{
    protected $table = 'assets';

    protected $fillable = ['name','model_number', 'expiry_date', 'price', 'is_fuel_dependent', 'litre_per_unit','is_active','asset_types_id', 'electricity_per_unit','quantity','rent_per_day'];

    public function assetTypes(){
        return $this->belongsTo('App\AssetType','asset_types_id');
    }
}