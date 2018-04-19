<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankInfoTransaction extends Model
{
    protected $table = "bank_info_transactions";

    protected $fillable = ['user_id','bank_id','amount','payment_type_id','date','reference_number','remark'];

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    public function bank(){
        return $this->belongsTo('App\BankInfo','bank_id');
    }

    public function paymentType(){
        return $this->belongsTo('App\PaymentType','payment_type_id');
    }
}
