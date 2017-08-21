<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillQuotationExtraItem extends Model
{
    protected $table = 'bill_quotation_extra_items';

    protected $fillable = ['bill_id','quotation_extra_item_id','description','rate'];

    public function quotationExtraItems(){
        return $this->belongsTo('App\QuotationExtraItem' , 'quotation_extra_item_id');
    }
}
