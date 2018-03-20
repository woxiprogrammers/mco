<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubcontractorAdvancePayment extends Model
{
    protected $table = 'subcontractor_advance_payments';

    protected $fillable = ['subcontractor_id','payment_id','amount','reference_number'];

    public function paymentType(){
        return $this->belongsTo('App\PaymentType','payment_id');
    }

    public function subcontractor(){
        return $this->belongsTo('App\Subcontractor','subcontractor_id');
    }
}