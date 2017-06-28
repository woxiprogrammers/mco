<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotationWorkOrder extends Model
{
    protected $table = 'quotation_work_orders';

    protected $fillable = ['quotation_id','work_order_number','description','scope','order_value'];

    public function images(){
        return $this->hasMany('App\WorkOrderImage','quotation_work_order_id');
    }
}
