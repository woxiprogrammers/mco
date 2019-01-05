<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubcontractorBillTransaction extends Model
{
    protected $table = 'subcontractor_bill_transactions';

    protected $fillable = ['subcontractor_bills_id','subtotal','total','debit','hold','retention_percent',
        'retention_amount','tds_percent','tds_amount','other_recovery','remark','is_advance','bank_id',
        'payment_type_id','paid_from_slug', 'payment_mode_slug', 'transaction_status_id','status_remark','created_at'
    ];

    public function subcontractorBill(){
        return $this->belongsTo('App\SubcontractorBill','subcontractor_bills_id');
    }

    public function subcontractorBillReconcileTransaction(){
        return $this->hasMany('App\SubcontractorBillReconcileTransaction','subcontractor_bill_id');
    }

    public function transactionStatus(){
        return $this->belongsTo('App\TransactionStatus', 'transaction_status_id');
    }
}
