<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderTransactionBackup extends Model
{
    protected $table = 'purchase_order_transactions_backup';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = ['id','purchase_order_id','purchase_order_transaction_status_id','bill_number','vehicle_number','grn',
        'in_time','out_time','remark','bill_amount','created_at','updated_at'];
}
