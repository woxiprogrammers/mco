<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteTransferBillChallan extends Model
{
    protected $table = 'site_transfer_bill_challans';

    protected $fillable = [
        'site_transfer_bill_id', 'inventory_transfer_challan_id'
    ];

    public function siteTransferBill()
    {
        return $this->hasMany('App\SiteTransferBillImage', 'site_transfer_bill_id');
    }

    public function inventoryTransferChallan()
    {
        return $this->belongsTo('App\InventoryTransferChallan', 'inventory_transfer_challan_id');
    }
}
