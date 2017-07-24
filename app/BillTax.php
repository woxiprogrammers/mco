<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillTax extends Model
{
    protected $table = 'bill_taxes';

    protected $fillable = ['bill_id','tax_id','percentage','applied_on'];

    public function taxes()
    {
        return $this->belongsTo('App\Tax','tax_id');
    }
}
