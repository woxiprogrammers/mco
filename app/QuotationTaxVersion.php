<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotationTaxVersion extends Model
{
    protected $table = "quotation_tax_versions";

    protected $fillable = ['tax_id','quotation_id','percentage'];

    public function quotation(){
        return $this->belongsTo('App\Quotation','quotation_id');
    }

    public function tax(){
        return $this->belongsTo('App\Tax','tax_id');
    }
}
