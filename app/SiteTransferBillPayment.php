<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteTransferBillPayment extends Model
{
    protected $table = 'site_transfer_bill_payments';

    protected $fillable = ['site_transfer_bill_id','payment_type_id','amount','reference_number','remark'];

    public function siteTransferBill(){
        return $this->belongsTo('App\SiteTransferBill','site_transfer_bill_id');
    }

    public function paymentType(){
        return $this->belongsTo('App\PaymentType','payment_type_id');
    }
}
