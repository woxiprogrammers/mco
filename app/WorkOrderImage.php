<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkOrderImage extends Model
{
    protected $table = 'work_order_images';

    protected $fillable = ['quotation_work_order_id','image'];

    public function quotation_work_order(){
        return $this->belongsTo('App\QuotationWorkOrder','quotation_work_order_id');
    }
}
