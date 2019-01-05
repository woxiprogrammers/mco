<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubcontractorStructureExtraItem extends Model
{
    protected $table = 'subcontractor_structure_extra_items';

    protected $fillable = ['subcontractor_structure_id', 'extra_item_id', 'rate'];

    public function extraItem(){
        return $this->belongsTo('App\ExtraItem', 'extra_item_id');
    }

    public function subcontractorStructure(){
        return $this->belongsTo('App\SubcontractorStructure', 'subcontractor_structure_id');
    }

    public function subcontractorBillExtraItems(){
        return $this->hasMany('App\SubcontractorBillExtraItem', 'subcontractor_structure_extra_item_id');
    }
}
