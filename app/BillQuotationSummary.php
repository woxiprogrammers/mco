<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillQuotationSummary extends Model
{
    protected $table = 'bill_quotation_summaries';

    protected $fillable = ['bill_id','quotation_summary_id','rate_per_sqft','built_up_area','quantity'
        ,'is_deleted','product_description_id','sub_total','with_tax_amount','rounded_amount_by'];
}
