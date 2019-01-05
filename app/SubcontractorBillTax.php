<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubcontractorBillTax extends Model
{
    protected $table = 'subcontractor_bill_taxes';

    protected $fillable = ['subcontractor_bills_id','tax_id','percentage', 'applied_on'];

    public function tax(){
        return $this->belongsTo('App\Tax','tax_id');
    }
}
