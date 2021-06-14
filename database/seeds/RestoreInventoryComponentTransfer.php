<?php

use App\InventoryComponentTransfers;
use App\InventoryComponentTransfersBackup;
use Illuminate\Database\Seeder;

class RestoreInventoryComponentTransfer extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inventoryComponents = InventoryComponentTransfersBackup::all();
        foreach($inventoryComponents as $inventory) {
            $iCTInfo = $inventory->toArray();
            $newICT = InventoryComponentTransfers::firstOrNew(['id' => $inventory->id]);
            
            $newICT->id = $iCTInfo['id'];
            $newICT->inventory_component_id = $iCTInfo['inventory_component_id'];
            $newICT->transfer_type_id = $iCTInfo['transfer_type_id'];
            $newICT->quantity = $iCTInfo['quantity'];
            $newICT->unit_id = $iCTInfo['unit_id'];
            $newICT->remark = $iCTInfo['remark'];
            $newICT->source_name = $iCTInfo['source_name'];
            $newICT->bill_number = $iCTInfo['bill_number'];
            $newICT->bill_amount = $iCTInfo['bill_amount'];
            $newICT->vehicle_number = $iCTInfo['vehicle_number'];
            $newICT->in_time = $iCTInfo['in_time'];
            $newICT->out_time = $iCTInfo['out_time'];
            $newICT->payment_type_id = $iCTInfo['payment_type_id'];
            $newICT->date = $iCTInfo['date'];
            $newICT->next_maintenance_hour = $iCTInfo['next_maintenance_hour'];
            $newICT->user_id = $iCTInfo['user_id'];
            $newICT->comment_data = $iCTInfo['comment_data'];
            $newICT->grn = $iCTInfo['grn'];
            $newICT->inventory_component_transfer_status_id = $iCTInfo['inventory_component_transfer_status_id'];
            $newICT->rate_per_unit = $iCTInfo['rate_per_unit'];
            $newICT->cgst_percentage = $iCTInfo['cgst_percentage'];
            $newICT->sgst_percentage = $iCTInfo['sgst_percentage'];
            $newICT->igst_percentage = $iCTInfo['igst_percentage'];
            $newICT->cgst_amount = $iCTInfo['cgst_amount'];
            $newICT->sgst_amount = $iCTInfo['sgst_amount'];
            $newICT->igst_amount = $iCTInfo['igst_amount'];
            $newICT->total = $iCTInfo['total'];
            $newICT->vendor_id = $iCTInfo['vendor_id'];
            $newICT->transportation_amount = $iCTInfo['transportation_amount'];
            $newICT->transportation_cgst_percent = $iCTInfo['transportation_cgst_percent'];
            $newICT->transportation_sgst_percent = $iCTInfo['transportation_sgst_percent'];
            $newICT->transportation_igst_percent = $iCTInfo['transportation_igst_percent'];
            $newICT->driver_name = $iCTInfo['driver_name'];
            $newICT->mobile = $iCTInfo['mobile'];
            $newICT->related_transfer_id = $iCTInfo['related_transfer_id'];
            $newICT->inventory_transfer_challan_id = $iCTInfo['inventory_transfer_challan_id'];
            $newICT->created_at = $iCTInfo['created_at'];
            $newICT->updated_at = $iCTInfo['updated_at'];
            $newICT->save();
            $inventory->delete();
        }
    }
}
