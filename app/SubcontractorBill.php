<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubcontractorBill extends Model
{
    protected $table = 'subcontractor_bills';

    protected $fillable = ['sc_structure_id','subcontractor_bill_status_id','qty','description'];
}
