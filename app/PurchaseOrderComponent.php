<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderComponent extends Model
{
    protected $table = 'purchase_order_components';

    protected $fillable = ['purchase_order_id','purchase_request_component_id','rate_per_unit','gst',
        'hsn_code','expected_delivery_date','remark','credited_days','quantity','unit_id',
        'cgst_percentage','sgst_percentage','igst_percentage','cgst_amount','sgst_amount','igst_amount','total'];

    public function purchaseRequestComponent(){
        return $this->belongsTo('App\PurchaseRequestComponent' , 'purchase_request_component_id');
    }

    public function purchaseOrder(){
        return $this->belongsTo('App\PurchaseOrder','purchase_order_id');
    }

    public function purchaseOrderTransactionComponent(){
        return $this->hasMany('App\PurchaseOrderTransactionComponent','purchase_order_component_id');
    }
}
