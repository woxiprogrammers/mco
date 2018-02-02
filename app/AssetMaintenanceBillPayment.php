<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetMaintenanceBillPayment extends Model
{
    protected $table = 'asset_maintenance_bill_payments';

    protected $fillable = ['asset_maintenance_bill_id','amount','payment_id','reference_number','is_advance'];

    public function assetMaintenanceBill(){
        return $this->belongsTo('App\AssetMaintenanceBill','asset_maintenance_bill_id');
    }
}
