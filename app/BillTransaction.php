<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillTransaction extends Model
{
    protected $table = 'bill_transactions';

    protected $fillable = ['bill_id','total','remark','debit','hold','paid_from_advanced','retention_percent','retention_amount',
        'tds_percent','tds_amount','amount','other_recovery_value','bank_id','payment_type_id'
    ];

    public function bill(){
        return $this->belongsTo('App\Bill','bill_id');
    }
}
