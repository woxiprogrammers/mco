<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $table = 'bills';

    protected $fillable = ['quotation_id','bill_status_id','remark','date','performa_invoice_date','discount_amount'
        ,'discount_description','bank_info_id','subtotal','rounded_amount_by','gross_total'];

    public function quotation(){
        return $this->belongsTo('App\Quotation','quotation_id');
    }

    public function bill_quotation_product(){
        return $this->hasMany('App\BillQuotationProducts','bill_id');
    }

    public function bill_status(){
        return $this->belongsTo('App\BillStatus','bill_status_id');
    }

    public function bill_tax(){
        return $this->hasMany('App\BillTax','bill_id');
    }

    public function transactions(){
        return $this->hasMany('App\BillTransaction','bill_id');
    }

    public function bankInfo(){
        return $this->belongsTo('App\BankInfo','bank_info_id');
    }

    public function bill_quotation_extraItems(){
        return $this->hasMany('App\BillQuotationExtraItem','bill_id');
    }

    public function billReconcileTransaction(){
        return $this->hasMany('App\BillReconcileTransaction','bill_id');
    }

    public function billQuotationSummary(){
        return $this->hasMany('App\BillQuotationSummary','bill_id');
    }
}
