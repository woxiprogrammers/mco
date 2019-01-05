<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubcontractorStructure extends Model
{
    protected $table = 'subcontractor_structure';

    protected $fillable = ['project_site_id', 'subcontractor_id',
        'summary_id' , 'sc_structure_type_id', 'rate' ,'total_work_area',
        'description','cancelled_bill_transaction_total_amount','cancelled_bill_transaction_balance_amount',];

    public function projectSite(){
        return $this->belongsTo('App\ProjectSite','project_site_id');
    }

    public function contractType() {
        return $this->belongsTo('App\SubcontractorStructureType','sc_structure_type_id');
    }

    public function summary() {
        return $this->belongsTo('App\Summary','summary_id');
    }

    public function subcontractor() {
        return $this->belongsTo('App\Subcontractor','subcontractor_id');
    }

    public function subcontractorBill() {
        return $this->hasMany('App\SubcontractorBill','sc_structure_id');
    }

    public function summaries(){
        return $this->hasMany('App\SubcontractorStructureSummary', 'subcontractor_structure_id');
    }

    public function extraItems(){
        return $this->hasMany('App\SubcontractorStructureExtraItem', 'subcontractor_structure_id');
    }
}
