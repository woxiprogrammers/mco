<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillTax extends Model
{
    protected $table = 'bill_taxes';

    protected $fillable = ['bill_id','tax_id','percentage'];
}
