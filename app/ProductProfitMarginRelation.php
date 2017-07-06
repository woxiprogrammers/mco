<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductProfitMarginRelation extends Model
{
    protected $table = 'products_profit_margins_relation';

    protected $fillable = ['product_version_id','profit_margin_version_id'];

    public function profit_margin_version(){
        return $this->belongsTo('App\ProfitMarginVersion','profit_margin_version_id');
    }

    public function product_version(){
        return $this->belongsTo('App\ProductVersion','product_version_id');
    }
}
