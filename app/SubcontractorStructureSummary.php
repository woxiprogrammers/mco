<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubcontractorStructureSummary extends Model
{
    protected $table = 'subcontractor_structure_summaries';

    protected $fillable = ['subcontractor_structure_id', 'rate', 'total_work_area', 'description',
        'summary_id', 'unit_id'
    ];

    public function subcontractorStructure(){
        return $this->belongsTo('App\SubcontractorStructure', 'subcontractor_structure_id');
    }

    public function summary(){
        return $this->belongsTo('App\Summary', 'summary_id');
    }

    public function subcontractorBillSummaries(){
        return $this->hasMany('App\SubcontractorBillSummary', 'subcontractor_structure_summary_id');
    }

    public function unit(){
        return $this->belongsTo('App\Unit','unit_id');
    }
}
