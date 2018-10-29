<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubcontractorBillSummary extends Model
{
    protected $table = 'subcontractor_bill_summaries';

    protected $fillable = ['subcontractor_bill_id', 'subcontractor_structure_summary_id', 'quantity',
        'total_work_area', 'description', 'number_of_floors', 'is_deleted'
    ];

    public function subcontractorBill(){
        return $this->belongsTo('App\SubcontractorBill', 'subcontractor_bill_id');
    }

    public function subcontractorStructureSummary(){
        return $this->belongsTo('App\SubcontractorStructureSummary', 'subcontractor_structure_summary_id');
    }
}
