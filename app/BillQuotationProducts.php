<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillQuotationProducts extends Model
{
    protected $table = 'bill_quotation_products';

    protected $fillable = ['bill_id','quotation_product_id','quantity'];
}
