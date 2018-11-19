<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubcontractorBillExtraItem extends Model
{
    protected $table = 'subcontractor_bill_extra_items';

    protected $fillable = ['subcontractor_bill_id', 'subcontractor_structure_extra_item_id', 'rate','description'];

    public function subcontractorBill(){
        return $this->belongsTo('App\SubcontractorBill', 'subcontractor_bill_id');
    }

    public function subcontractorStructureExtraItem(){
        return $this->belongsTo('App\SubcontractorStructureExtraItem', 'subcontractor_structure_extra_item_id');
    }
}
