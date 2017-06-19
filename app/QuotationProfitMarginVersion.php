<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotationProfitMarginVersion extends Model
{
    protected $table = 'quotation_profit_margin_versions';

    protected $fillable = ['profit_margin_id','percentage','quotation_product_id'];

    public function quotation(){
        return $this->belongsTo('App\Quotation','quotation_id');
    }
}
