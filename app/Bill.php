<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $table = 'bills';

    protected $fillable = ['quotation_id','bill_status_id','remark','date','performa_invoice_date','discount_amount','discount_description'];

    public function quotation()
    {
        return $this->belongsTo('App\Quotation','quotation_id');
    }
    public function bill_quotation_product()
    {
        return $this->hasMany('App\BillQuotationProducts','bill_id');
    }
    public function bill_status()
    {
        return $this->belongsTo('App\BillStatus','bill_status_id');
    }
    public function bill_tax()
    {
        return $this->hasMany('App\BillTax','bill_id');
    }

    public function transactions(){
        return $this->hasMany('App\BillTransaction','bill_id');
    }
}
