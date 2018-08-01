<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteTransferBill extends Model
{
    protected $table = 'site_transfer_bills';

    protected $fillable = ['inventory_component_transfer_id','bill_number','bill_date','subtotal','tax_amount',
        'extra_amount','extra_amount_cgst_percentage','extra_amount_sgst_percentage','extra_amount_igst_percentage',
        'extra_amount_cgst_amount','extra_amount_sgst_amount','extra_amount_igst_amount','total','remark','format_id','created_at'
    ];

    public function inventoryComponentTransfer(){
        return $this->belongsTo('App\InventoryComponentTransfers','inventory_component_transfer_id');
    }

    public function siteTransferBillImages(){
        return $this->hasMany('App\SiteTransferBillImage','site_transfer_bill_id');
    }
}
