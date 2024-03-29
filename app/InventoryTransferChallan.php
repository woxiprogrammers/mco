<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class InventoryTransferChallan extends Model
{
    protected $table = 'inventory_transfer_challan';

    protected $fillable = [
        'challan_number', 'project_site_out_id', 'project_site_in_id', 'project_site_out_date', 'project_site_in_date', 'inventory_component_transfer_status_id'
    ];

    public function projectSiteOut()
    {
        return $this->belongsTo('App\ProjectSite', 'project_site_out_id');
    }

    public function projectSiteIn()
    {
        return $this->belongsTo('App\ProjectSite', 'project_site_in_id');
    }

    public function inventoryComponentTransferStatus()
    {
        return $this->belongsTo('App\InventoryComponentTransferStatus', 'inventory_component_transfer_status_id');
    }

    public function otherData()
    {
        $outTransferTypeId = InventoryTransferTypes::where('slug', 'site')->where('type', 'OUT')->pluck('id')->first();
        $data = InventoryComponentTransfers::where('inventory_transfer_challan_id', $this['id'])->where('transfer_type_id', $outTransferTypeId)
            ->select(
                'bill_number',
                'date',
                'vendor_id',
                'transportation_amount',
                'transportation_cgst_percent',
                'transportation_sgst_percent',
                'transportation_igst_percent',
		        'inventory_component_transfer_status_id',
                'driver_name',
                'mobile',
                'vehicle_number'
            )->first();
	    $data['status_of_challan'] =  InventoryComponentTransferStatus::where('id','=',$this['inventory_component_transfer_status_id'])->pluck('name')->first();

        if ($data['status_of_challan'] == NULL) {
            $data['vendor_name'] = "";
        } else {
            if(isset($data['vendor_id'])) {
                $data['vendor_name'] = Vendor::where('id', $data['vendor_id'])->pluck('name')->first();
            } else {
                $data['vendor_name'] = "";
            }
        }


        $data['transportation_total'] = $data['transportation_tax_total'] = 0;
        if(isset($data['transportation_amount'])) {
            if ($data['transportation_amount'] != null && $data['transportation_amount'] != "0") {
                $transportation_amount = $data['transportation_amount'];
                $cgstAmount = $transportation_amount * ($data['transportation_cgst_percent'] / 100) ?? 0;
                $sgstAmount = $transportation_amount * ($data['transportation_sgst_percent'] / 100) ?? 0;
                $igstAmount = $transportation_amount * ($data['transportation_igst_percent'] / 100) ?? 0;
                $data['transportation_tax_total'] = $cgstAmount + $sgstAmount + $igstAmount;
                $data['transportation_total'] = $transportation_amount + $cgstAmount + $sgstAmount + $igstAmount;
            }
        } else {
            $data['transportation_amount'] = $data['transportation_cgst_percent'] = $data['transportation_sgst_percent'] = $data['transportation_igst_percent'] = 0;
        }

        return $data;
    }

    public function getRemark()
    {
        $outTransferTypeId = InventoryTransferTypes::where('slug', 'site')->where('type', 'OUT')->pluck('id')->first();
        $remark = InventoryComponentTransfers::where('inventory_transfer_challan_id', $this['id'])->where('transfer_type_id', $outTransferTypeId)->pluck('remark')->first();
        return $remark;
    }

    public function getCreatedBy()
    {
        $outTransferTypeId = InventoryTransferTypes::where('slug', 'site')->where('type', 'OUT')->pluck('id')->first();
        $outTransfer = InventoryComponentTransfers::where('inventory_transfer_challan_id', $this['id'])->where('transfer_type_id', $outTransferTypeId)->first();
        $user = [
            'id'    => $outTransfer['user_id'],
            'name'  => $outTransfer->user->first_name . ' ' . $outTransfer->user->last_name
        ];
        return $user;
    }
}
