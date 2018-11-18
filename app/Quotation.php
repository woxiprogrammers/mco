<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = 'quotations';

    protected $fillable = ['project_site_id','quotation_status_id','remark','is_tax_applied','is_summary_applied'
        ,'discount','built_up_area','carpet_area','opening_expenses','bill_type_id','cancelled_bill_transaction_total_amount','cancelled_bill_transaction_balance_amount'];

    public function project_site(){
        return $this->belongsTo('App\ProjectSite','project_site_id','id');
    }

    public function quotation_status(){
        return $this->belongsTo('App\QuotationStatus','quotation_status_id','id');
    }

    public function quotation_products(){
        return $this->hasMany('App\QuotationProduct','quotation_id');
    }

    public function quotation_materials(){
        return $this->hasMany('App\QuotationMaterial','quotation_id');
    }
    public function tax_version(){
        return $this->hasMany('App\QuotationTaxVersion','quotation_id');
    }

    public function work_order(){
        return $this->hasOne('App\QuotationWorkOrder','quotation_id');
    }

    public function quotation_floor(){
        return $this->hasMany('App\QuotationFloor','quotation_id');
    }

    public function billType(){
        return $this->belongsTo('App\SubcontractorStructureType','bill_type_id');
    }

    public function bill(){
        return $this->hasMany('App\Bill','quotation_id');
    }
}
