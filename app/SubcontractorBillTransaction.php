<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubcontractorBillTransaction extends Model
{
    protected $table = 'subcontractor_bill_transactions';

    protected $fillable = ['subcontractor_bills_id','subtotal','total','debit','hold','retention_percent','retention_amount','tds_percent','tds_amount','other_recovery','remark'];
}
