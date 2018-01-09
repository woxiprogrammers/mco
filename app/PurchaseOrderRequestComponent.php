<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderRequestComponent extends Model
{
    protected $table = 'purchase_order_request_components';

    protected $fillable = ['purchase_order_request_id','is_approved','purchase_request_component_id',
        'rate_per_unit','gst','hsn_code','expected_delivery_date','remark','credited_days',
        'quantity','unit_id','cgst_percentage','sgst_percentage','igst_percentage','cgst_amount',
        'sgst_amount','igst_amount','total'
    ];

    public function purchaseOrderRequest(){
        return $this->belongsTo('App\PurchaseOrderRequest','purchase_order_request_id');
    }

    public function purchaseRequestComponent(){
        return $this->belongsTo('App\PurchaseRequestComponent','purchase_request_component_id');
    }

    public function purchaseOrderRequestComponentImages(){
        return $this->hasMany('App\PurchaseRequestComponentImage','purchase_order_request_component_id');
    }
}
