<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderBill extends Model
{
    protected $table = 'purchase_order_bills';

    protected $fillable = [
        'purchase_order_id','amount','extra_amount','bill_number','tax_amount','transportation_tax_amount','transportation_total_amount'
    ];

    public function purchaseOrder(){
        return $this->belongsTo('App\PurchaseOrder','purchase_order_id');
    }

    public function purchaseOrderTransactionRelation(){
        return $this->hasMany('App\PurchaseOrderBillTransactionRelation','purchase_order_bill_id');
    }

    public function purchaseOrderPayment(){
        return $this->hasMany('App\PurchaseOrderPayment','purchase_order_bill_id');
    }
}
