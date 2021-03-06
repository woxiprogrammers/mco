<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubcontractorBill extends Model
{
    protected $table = 'subcontractor_bills';

    protected $fillable = ['sc_structure_id','subcontractor_bill_status_id','qty','description',
        'number_of_floors', 'discount', 'discount_description', 'subtotal', 'round_off_amount',
        'grand_total', 'bill_date', 'performa_invoice_date'
    ];

    public function subcontractorBillStatus(){
        return $this->belongsTo('App\SubcontractorBillStatus','subcontractor_bill_status_id');
    }

    public function subcontractorStructure(){
        return $this->belongsTo('App\SubcontractorStructure','sc_structure_id');
    }

    public function subcontractorBillTaxes(){
        return $this->hasMany('App\SubcontractorBillTax','subcontractor_bills_id');
    }

    public function subcontractorBillTransaction(){
        return $this->hasMany('App\SubcontractorBillTransaction','subcontractor_bills_id');
    }

    public function subcontractorBillReconcileTransaction(){
        return $this->hasMany('App\SubcontractorBillReconcileTransaction','subcontractor_bill_id');
    }

    public function subcontractorBillSummaries(){
        return $this->hasMany('App\SubcontractorBillSummary','subcontractor_bill_id');
    }

    public function subcontractorBillExtraItems(){
        return $this->hasMany('App\SubcontractorBillExtraItem','subcontractor_bill_id');
    }
}
