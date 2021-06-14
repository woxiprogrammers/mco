<?php

use App\InventoryComponentTransfers;
use App\InventoryComponentTransfersBackup;
use App\PurchaseOrderTransaction;
use App\PurchaseOrderTransactionBackup;
use Illuminate\Database\Seeder;

class DeleteZombieICTGrn extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $potGrn = PurchaseOrderTransaction::pluck('grn');
        /*$potBackupGrn = PurchaseOrderTransactionBackup::pluck('grn');
        $ictGrn = InventoryComponentTransfers::whereIn('grn',$potBackupGrn)->get();

        foreach($ictGrn as $ict) {
            InventoryComponentTransfersBackup::create($ict->toArray());
            $ict->delete();
        }*/
        //$ictGrn = InventoryComponentTransfers::leftJoin('purchase_order_transactions','purchase_order_transactions.grn','=','inventory_component_transfers.grn')
        //            ->where('purchase_order_transactions.grn','!=','inventory_component_transfers.grn')->get();
        // $potBackupGrn = PurchaseOrderTransaction::whereNotIn('grn',$ictGrn)->count();
        /*foreach($ictGrn as $grn) {
            dd($grn);
        }*/

        $ictGrn = InventoryComponentTransfers::pluck('grn');
        $potGrn = PurchaseOrderTransaction::pluck('grn');
        $potGrn = $potGrn->toArray();
        $grnMaster = [];
        foreach($ictGrn as $grn) {
            if (!in_array($grn, $potGrn)) {
                $grnMaster[] = $grn;
            }
        }

        $inventoryComponents = InventoryComponentTransfers::whereIn('grn',$grnMaster)->get();
        foreach($inventoryComponents as $inventory) {
            InventoryComponentTransfersBackup::create($inventory->toArray());
            $inventory->delete();
        }
    }
}
