<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillQuotationProducts extends Model
{
    protected $table = 'bill_quotation_products';

    protected $fillable = ['bill_id','quotation_product_id','quantity','product_description_id'];

    public function quotation_products()
    {
        return $this->belongsTo('App\QuotationProduct','quotation_product_id');
    }

    public function product_description(){
        return $this->belongsTo('App\ProductDescription' ,'product_description_id');
    }

    public function bill(){
        return $this->belongsTo('App\Bill','id');
    }
}
