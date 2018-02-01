<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetMaintenanceBill extends Model
{
    protected $table = 'asset_maintenance_bills';

    protected $fillable = ['asset_maintenance_id','amount','cgst_percentage','cgst_amount','sgst_percentage','sgst_amount','igst_percentage','igst_amount','extra_amount','bill_number'];

    public function assetMaintenance(){
        return $this->belongsTo('App\AssetMaintenance','asset_maintenance_id');
    }
}
