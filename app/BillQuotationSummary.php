<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillQuotationSummary extends Model
{
    protected $table = 'bill_quotation_summaries';

    protected $fillable = ['bill_id','quotation_summary_id','rate_per_sqft','built_up_area','quantity'
        ,'is_deleted','product_description_id'];

    public function quotationSummary(){
        return $this->belongsTo('App\QuotationSummary','quotation_summary_id');
    }

    public function productDescription(){
        return $this->belongsTo('App\ProductDescription','product_description_id');
    }
}
