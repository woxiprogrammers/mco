<?php

namespace App\Console\Commands;

use App\Bill;
use App\BillQuotationProducts;
use App\BillTransaction;
use App\Quotation;
use App\QuotationProduct;
use App\QuotationSummary;
use App\SubcontractorBillTransaction;
use App\SubcontractorStructureType;
use App\TransactionStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BillModuleChanges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:bill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle(){
        /*$quotationModel = new Quotation();
        $quotationProductModel = new QuotationProduct();
        $quotationSummaryModel = new QuotationSummary();
        $bill = new Bill();
        $subcontractorStructureType = new SubcontractorStructureType();
        $quotationIds = $bill->pluck('quotation_id')->toArray();
        $quotationModel->whereIn('id', array_unique($quotationIds))->update([
            'bill_type_id' => $subcontractorStructureType->where('slug', 'itemwise')->pluck('id')->first()
        ]);
        $allQuotation = $quotationModel->all();
        foreach ($allQuotation as $quotation) {
            $summaryIds = $quotationProductModel->where('quotation_id', $quotation['id'])->whereNotNull('summary_id')->distinct('summary_id')
                                ->pluck('summary_id')->toArray();
            if(count($summaryIds) > 0){
                foreach($summaryIds as $summaryId){
                    $quotationSummaryProductData = $quotationProductModel->where('quotation_id',$quotation['id'])
                        ->where('summary_id',$summaryId)->get();
                    if((!empty($quotation['built_up_area']))){
                        $summaryAmount = $quotationSummaryProductData->sum(function($quotationSummaryProduct) {
                            $discounted_price_per_product = round(($quotationSummaryProduct->rate_per_unit - ($quotationSummaryProduct->rate_per_unit * ($quotationSummaryProduct->quotation->discount / 100))),3);
                            $discounted_price = $quotationSummaryProduct->quantity * $discounted_price_per_product;
                            return $discounted_price;
                        });
                        $ratePerSQFT = round(($summaryAmount / $quotation['built_up_area']),3);
                    }else{
                        $ratePerSQFT = 0.000;
                    }
                    $alreadyPresentSummary = $quotationSummaryModel->where('quotation_id',$quotation['id'])
                        ->where('summary_id',$summaryId)->first();

                    if($alreadyPresentSummary == null){
                        $quotationSummaryModel->create([
                            'quotation_id' => $quotation['id'],
                            'summary_id' => $summaryId,
                            'rate_per_sqft' => $ratePerSQFT
                        ]);
                    }else{
                        $alreadyPresentSummary->update([
                            'rate_per_sqft' => $ratePerSQFT
                        ]);
                    }
                }
            }
        }*/
        /*$transactionStatus = new TransactionStatus();
        $billTransaction = new BillTransaction();
        $subcontractorBillTransaction = new SubcontractorBillTransaction();
        $approvedStatusId = $transactionStatus->where('slug','approved')->pluck('id')->first();
        $billTransaction->all()->each(function($thisBillTransaction) use ($approvedStatusId){
           return $thisBillTransaction->update([
                'transaction_status_id' => $approvedStatusId
            ]);
        });
        $subcontractorBillTransaction->all()->each(function($thisBillTransaction) use ($approvedStatusId){
           return $thisBillTransaction->update([
                'transaction_status_id' => $approvedStatusId
            ]);
        });*/
        $billQuotationProduct = new BillQuotationProducts();
        $billQuotationProducts = $billQuotationProduct->all();
        foreach ($billQuotationProducts as $billQuotationProduct){
            $billQuotationProduct->update([
                'rate_per_unit' => $billQuotationProduct->quotation_products->rate_per_unit
            ]);
        }

    }
}
