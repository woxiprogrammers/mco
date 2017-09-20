<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotationBankInfo extends Model
{
    protected $table = "quotation_bank_info";

    protected $fillable = ['quotation_id','bank_info_id'];

    public function bankInfo(){
        return $this->belongsTo('App\BankInfo' , 'bank_info_id','id');
    }
}
