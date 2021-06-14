<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryComponentTransfersBackup extends Model
{
    protected $table = 'inventory_component_transfers_backup';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = [
        'id','inventory_component_id', 'transfer_type_id', 'quantity', 'unit_id', 'remark', 'source_name',
        'bill_number', 'bill_amount', 'vehicle_number', 'in_time', 'out_time', 'payment_type_id', 'date', 'next_maintenance_hour',
        'user_id', 'comment_data', 'grn', 'inventory_component_transfer_status_id', 'rate_per_unit',
        'cgst_percentage', 'sgst_percentage', 'igst_percentage', 'cgst_amount', 'sgst_amount', 'igst_amount', 'total',
        'vendor_id', 'transportation_amount', 'transportation_cgst_percent', 'transportation_sgst_percent', 'transportation_igst_percent', 'driver_name', 'mobile', 'related_transfer_id',
        'created_at', 'inventory_transfer_challan_id', 'updated_at'
    ];
}
