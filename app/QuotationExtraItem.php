<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotationExtraItem extends Model
{
    protected $table = 'quotation_extra_items';

    protected $fillable = ['quotation_id','extra_item_id','rate'];

    public function extraItem(){
        return $this->belongsTo('App\ExtraItem' , 'extra_item_id','id');
    }
}
