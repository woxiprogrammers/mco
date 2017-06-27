<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $table = 'bills';

    protected $fillable = ['quotation_id','bill_status_id','remark'];

    public function quotation()
    {
        return $this->belongsTo('App\Quotation','quotation_id');
    }
    public function bill_quotation_product()
    {
        return $this->hasMany('App\BillQuotationProducts','bill_id');
    }
}
