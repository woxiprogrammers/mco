<?php

namespace App\Console\Commands;

use App\ExtraItem;
use App\SubcontractorBill;
use App\SubcontractorBillSummary;
use App\SubcontractorBillTransaction;
use App\SubcontractorStructure;
use App\SubcontractorStructureExtraItem;
use App\SubcontractorStructureSummary;
use App\SubcontractorStructureType;
use App\TransactionStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SubcontractorModuleMerge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:subcontractor-module-merge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'TO BE RUN ONLY ONCE. Command to merge old subcontractor module with new.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{
            $subcontractorStructures = SubcontractorStructure::whereNotNull('summary_id')->get();
            /* Merging subcontractor structure flow */
            foreach($subcontractorStructures as $subcontractorStructure){
                $extraItemIds = ExtraItem::where('is_active', true)->pluck('id');
                $subcontractorStructureData = [
                    'summary_id' => null,
                    'description' => null,
                    'total_work_area' => null,
                    'rate' => null
                ];
                $subcontractorStructureSummaryData = [
                    'subcontractor_structure_id' => $subcontractorStructure['id'],
                    'summary_id' => $subcontractorStructure['summary_id'],
                    'description' => $subcontractorStructure['description'],
                    'total_work_area' => $subcontractorStructure['total_work_area'],
                    'rate' => $subcontractorStructure['rate']
                ];
                $subcontractorStructure->update($subcontractorStructureData);
                $subcontractorStructureSummary = SubcontractorStructureSummary::create($subcontractorStructureSummaryData);
                foreach($extraItemIds as $extraItemId){
                    $subcontractorStructureExtraItemData = [
                        'subcontractor_structure_id' => $subcontractorStructure['id'],
                        'extra_item_id' => $extraItemId,
                        'rate' => 0
                    ];
                    $subcontractorStructureExtraItem = SubcontractorStructureExtraItem::create($subcontractorStructureExtraItemData);
                }
                $subcontractorBills = SubcontractorBill::where('sc_structure_id', $subcontractorStructure['id'])->get();
                $approvedTransactionStatusId = TransactionStatus::where('slug', 'approved')->pluck('id')->first();
                foreach($subcontractorBills as $subcontractorBill){
                    if($subcontractorStructure->contractType->slug == 'amountwise'){
                        $rate = $subcontractorStructureSummary['rate'] * $subcontractorStructureSummary['total_work_area'];
                        $subtotal = round(($rate * $subcontractorBill['qty']),3);
                    }else{
                        $subtotal = round(($subcontractorStructureSummary['rate'] * $subcontractorBill['qty']),3);
                    }
                    $taxAmount = 0;
                    foreach ($subcontractorBill->subcontractorBillTaxes as $subcontractorBillTax){
                        $subcontractorBillTax->update(['applied_on' => json_encode([0])]);
                        $taxAmount += (($subtotal * $subcontractorBillTax['percentage']) / 100);
                    }
                    $grandTotal = $subtotal + $taxAmount;
                    $subcontractorBillData = [
                        'qty' => null,
                        'description' => null,
                        'number_of_floors' => null,
                        'subtotal' => $subtotal,
                        'discount' => 0,
                        'round_off_amount' => 0,
                        'grand_total' => $grandTotal
                    ];
                    $subcontractorBillSummaryData = [
                        'subcontractor_bill_id' => $subcontractorBill['id'],
                        'subcontractor_structure_summary_id' => $subcontractorStructureSummary['id'],
                        'quantity' => $subcontractorBill['qty'],
                        'total_work_area' => $subcontractorStructureSummary['total_work_area'],
                        'description' => $subcontractorBill['description'],
                        'number_of_floors' => $subcontractorBill['number_of_floors'],
                    ];
                    $subcontractorBill->update($subcontractorBillData);
                    $subcontractorBillSummary = SubcontractorBillSummary::create($subcontractorBillSummaryData);
                    SubcontractorBillTransaction::where('subcontractor_bills_id', $subcontractorBill['id'])
                        ->update(['transaction_status_id' => $approvedTransactionStatusId]);
                }
            }
            $this->info('Old Subcontractor structure module merged with new successfully');
        }catch (\Exception $e){
            $data = [
                'action' => 'Subcontractor module merge script',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $this->error($e->getMessage());
        }
    }
}
