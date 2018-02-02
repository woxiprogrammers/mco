<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetMaintenanceBillTransaction extends Model
{
    protected $table = 'asset_maintenance_bill_transaction_relation';

    protected $fillable = ['asset_maintenance_bill_id','asset_maintenance_transaction_id'];

    public function assetMaintenanceBill(){
        return $this->belongsTo('App\AssetMaintenanceBill','asset_maintenance_bill_id');
    }

    public function assetMaintenanceTransaction(){
        return $this->belongsTo('App\AssetMaintenanceTransaction','asset_maintenance_transaction_id');
    }
}
