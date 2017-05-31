<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfitMarginVersion extends Model
{
    protected $table = 'profit_margin_versions';

    protected $fillable = ['profit_margin_id','percentage','created_at','updated_at'];

    public function profitMargin(){
        $this->belongsTo('App\ProfitMargin');
    }
}
