<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductProfitMarginRelation extends Model
{
    protected $table = 'products_profit_margins_relation';

    protected $fillable = ['product_version_id','profit_margin_version_id'];
}
