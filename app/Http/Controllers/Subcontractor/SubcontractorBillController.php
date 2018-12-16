<?php

namespace App\Http\Controllers\Subcontractor;

use App\BankInfo;
use App\Http\Controllers\CustomTraits\PeticashTrait;
use App\PaymentType;
use App\SubcontractorBill;
use App\SubcontractorBillExtraItem;
use App\SubcontractorBillReconcileTransaction;
use App\SubcontractorBillStatus;
use App\SubcontractorBillSummary;
use App\SubcontractorBillTax;
use App\SubcontractorBillTransaction;
use App\SubcontractorStructureExtraItem;
use App\SubcontractorStructureType;
use App\Summary;
use App\Tax;
use App\TransactionStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SubcontractorBillController extends Controller
{
    use PeticashTrait;
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request, $subcontractorStructure){
        try{
            $subcontractorStructureId = $subcontractorStructure->id;
            return view('subcontractor.bill.manage')->with(compact('subcontractorStructureId'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Subcontractor Bill manage view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request, $subcontractorStructure){
        try{
            $subcontractorStructureSummaries = $subcontractorStructure->summaries->toArray();
            $iterator = 0;
            foreach($subcontractorStructureSummaries as $subcontractorStructureSummary){
                $subcontractorStructureSummaries[$iterator]['summary_name'] = Summary::where('id', $subcontractorStructureSummary['summary_id'])->pluck('name')->first();
                $subcontractorStructureSummaries[$iterator]['prev_quantity'] = SubcontractorBill::join('subcontractor_structure', 'subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                                                    ->join('subcontractor_bill_summaries','subcontractor_bill_summaries.subcontractor_bill_id','=','subcontractor_bills.id')
                                                    ->where('subcontractor_bill_summaries.subcontractor_structure_summary_id', $subcontractorStructureSummary['id'])
                                                    ->where('subcontractor_bills.sc_structure_id', $subcontractorStructure->id)
                                                    ->sum('quantity');
                if($subcontractorStructure->contractType->slug == 'amountwise') {
                    $subcontractorStructureSummaries[$iterator]['allowed_quantity'] = 1 - $subcontractorStructureSummaries[$iterator]['prev_quantity'];
                }else{
                    $subcontractorStructureSummaries[$iterator]['allowed_quantity'] = $subcontractorStructureSummary['total_work_area'] - $subcontractorStructureSummaries[$iterator]['prev_quantity'];
                }
                $iterator += 1;
            }
            $structureExtraItems = SubcontractorStructureExtraItem::join('extra_items', 'extra_items.id', '=', 'subcontractor_structure_extra_items.extra_item_id')
                                                    ->where('subcontractor_structure_extra_items.subcontractor_structure_id', $subcontractorStructure->id)
                                                    ->select('subcontractor_structure_extra_items.id as subcontractor_structure_extra_item_id', 'subcontractor_structure_extra_items.rate as rate','extra_items.name as name')
                                                    ->get()->toArray();
            $totalBillCount = $subcontractorStructure->subcontractorBill->count();
            $billName = "R.A. ".($totalBillCount + 1);
            $taxes = Tax::whereNotIn('slug',['vat'])->where('is_active',true)->where('is_special',false)->select('id','name','slug','base_percentage')->get();
            $specialTaxes = Tax::whereNotIn('slug',['vat'])->where('is_active',true)->where('is_special', true)->select('id','name','slug','base_percentage')->get();
            return view('subcontractor.bill.create')->with(compact('billName', 'taxes', 'subcontractorStructure', 'subcontractorStructureSummaries', 'structureExtraItems', 'specialTaxes'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get subcontractor bill create view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createBill(Request $request, $subcontractorStructure){
        try{
            $subcontractorBillData = $request->only('discount', 'discount_description', 'subtotal', 'round_off_amount', 'grand_total');
            $subcontractorBillData['sc_structure_id'] = $subcontractorStructure->id;
            $subcontractorBillData['subcontractor_bill_status_id'] = SubcontractorBillStatus::where('slug', 'draft')->pluck('id')->first();
            if($request->has('performa_invoice_date') && $request->performa_invoice_date != '' && $request->performa_invoice_date != null){
                $subcontractorBillData['performa_invoice_date'] = date('Y-m-d', strtotime($request->performa_invoice_date));
            }
            if($request->has('bill_date') && $request->bill_date != '' && $request->bill_date != null){
                $subcontractorBillData['bill_date'] = date('Y-m-d', strtotime($request->bill_date));
            }
            $subcontractorBill = SubcontractorBill::create($subcontractorBillData);
            $subcontractorBillSummaryData = [
                'subcontractor_bill_id' => $subcontractorBill->id
            ];
            foreach($request->structure_summaries as $structureSummaryId){
                $subcontractorBillSummaryData['subcontractor_structure_summary_id'] = $structureSummaryId;
                $subcontractorBillSummaryData['quantity'] = $request->quantity[$structureSummaryId];
                $subcontractorBillSummaryData['description'] = $request->description[$structureSummaryId];
                $subcontractorBillSummaryData['total_work_area'] = $request->total_work_area[$structureSummaryId];
                $subcontractorBillSummary = SubcontractorBillSummary::create($subcontractorBillSummaryData);
            }
            if($request->has('structure_extra_item_ids')){
                $subcontractorBillExtraItemData = [
                    'subcontractor_bill_id' => $subcontractorBill->id
                ];
                foreach($request->structure_extra_item_ids as $structureExtraItemId){
                    $subcontractorBillExtraItemData['subcontractor_structure_extra_item_id'] = $structureExtraItemId;
                    $subcontractorBillExtraItemData['rate'] = $request->structure_extra_item_rate[$structureExtraItemId];
                    $subcontractorBillExtraItemData['description'] = $request->structure_extra_item_description[$structureExtraItemId];
                    $subcontractorBillExtraItem = SubcontractorBillExtraItem::create($subcontractorBillExtraItemData);
                }
            }
            if($request->has('taxes')){
                $subcontractorBillTaxData = [
                    'subcontractor_bills_id' => $subcontractorBill->id
                ];
                foreach($request->taxes as $taxId => $percentage){
                    $subcontractorBillTaxData['tax_id'] = $taxId;
                    $subcontractorBillTaxData['percentage'] = $percentage;
                    $subcontractorBillTaxData['applied_on'] = json_encode([0]);
                    $subcontractorBillTax = SubcontractorBillTax::create($subcontractorBillTaxData);
                }
            }
            if($request->has('applied_on')){
                foreach ($request->applied_on as $specialTaxId => $specialTaxData){
                    if(array_key_exists('on', $specialTaxData)){
                        $subcontractorBillTaxData = [
                            'subcontractor_bills_id' => $subcontractorBill->id,
                            'tax_id' => $specialTaxId,
                            'percentage' => $specialTaxData['percentage']
                        ];
                        $subcontractorBillTaxData['applied_on'] = [];
                        foreach($specialTaxData['on'] as $taxId){
                            $subcontractorBillTaxData['applied_on'][] = $taxId;
                        }
                        $subcontractorBillTaxData['applied_on'] = json_encode($subcontractorBillTaxData['applied_on']);
                        $subcontractorBillTax = SubcontractorBillTax::create($subcontractorBillTaxData);
                    }
                }
            }
            $request->session()->flash('success', 'Subcontractor bill created successfully.');
            return redirect('/subcontractor/bill/manage/'.$subcontractorStructure->id);
        }catch (\Exception $e){
            $data = [
                'action' => 'subcontractor bill create',
                'subcontractor_structure' => $subcontractorStructure,
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function billListing(Request $request, $subcontractorStructure, $billStatusSlug){
        try{
            $records = array();
            $status = 200;
            $billArrayNo = 1;
            if($billStatusSlug == "disapproved"){
                $listingData = SubcontractorBill::join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                    ->where('subcontractor_structure.id',$subcontractorStructure->id)
                    ->whereNull('subcontractor_structure.summary_id')
                    ->where('subcontractor_bill_status_id',SubcontractorBillStatus::where('slug','disapproved')->pluck('id')->first())
                    ->orderBy('subcontractor_bills.id','asc')
                    ->select('subcontractor_bills.id','subcontractor_bills.qty','subcontractor_bills.subcontractor_bill_status_id','subcontractor_structure.sc_structure_type_id','subcontractor_structure.rate as rate', 'subcontractor_bills.discount as discount', 'subcontractor_bills.subtotal as subtotal', 'subcontractor_bills.grand_total as grand_total', 'subcontractor_bills.round_off_amount as round_off_amount')
                    ->get();
            }else{
                $listingData = SubcontractorBill::join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                    ->where('subcontractor_structure.id',$subcontractorStructure->id)
                    ->whereNull('subcontractor_structure.summary_id')
                    ->whereIn('subcontractor_bill_status_id',SubcontractorBillStatus::whereIn('slug',['approved','draft'])->pluck('id'))
                    ->orderBy('subcontractor_bills.id','asc')
                    ->select('subcontractor_bills.id','subcontractor_bills.qty','subcontractor_bills.subcontractor_bill_status_id','subcontractor_structure.sc_structure_type_id','subcontractor_structure.rate as rate', 'subcontractor_bills.discount as discount', 'subcontractor_bills.subtotal as subtotal', 'subcontractor_bills.grand_total as grand_total', 'subcontractor_bills.round_off_amount as round_off_amount')
                    ->get();
            }
            if ($request->has('get_total')) {
                $finalAmount = $paidAmount = 0;
                foreach($listingData as $data){
                    $finalAmount += $data['grand_total'];
                    $approvedStatusId = TransactionStatus::where('slug', 'approved')->pluck('id')->first();
                    $paidAmount += SubcontractorBillTransaction::where('subcontractor_bills_id', $data['id'])
                        ->where('transaction_status_id', $approvedStatusId)
                        ->sum('total');
                }
                $records['final_amount'] = $finalAmount;
                $records['paid_amount'] = $paidAmount;
                $records['pending_amount'] = round(($finalAmount - $paidAmount),3);
            }else{
                $iTotalRecords = count($listingData);
                $records = array();
                $records['data'] = array();
                $end = $request->length < 0 ? count($listingData) : $request->length;
                for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++,$pagination++ ){
                    $action = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="/subcontractor/bill/edit/'.$listingData[$pagination]->id.'">
                                         <i class="icon-docs"></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <a href="/subcontractor/bill/view/'.$listingData[$pagination]->id.'">
                                         <i class="icon-docs"></i>View
                                    </a>
                                </li>
                            </ul>';
                    $billStatus = $listingData[$pagination]->subcontractorBillStatus->name;
                    $basicAmount = round(($listingData[$pagination]->subtotal - $listingData[$pagination]->discount), 3);
                    $taxAmount = round(($listingData[$pagination]['grand_total'] - $basicAmount - $listingData[$pagination]['round_off_amount']), 3);
                    $finalAmount = $listingData[$pagination]['grand_total'];
                    $approvedStatusId = TransactionStatus::where('slug', 'approved')->pluck('id')->first();
                    $paidAmount = SubcontractorBillTransaction::where('subcontractor_bills_id', $listingData[$pagination]['id'])
                        ->where('transaction_status_id', $approvedStatusId)
                        ->sum('total');
                    $retentionAmount = SubcontractorBillTransaction::where('subcontractor_bills_id', $listingData[$pagination]['id'])
                        ->where('transaction_status_id', $approvedStatusId)->sum('retention_amount');
                    $tdsAmount = SubcontractorBillTransaction::where('subcontractor_bills_id', $listingData[$pagination]['id'])
                        ->where('transaction_status_id', $approvedStatusId)->sum('tds_amount');
                    $holdAmount = SubcontractorBillTransaction::where('subcontractor_bills_id', $listingData[$pagination]['id'])
                        ->where('transaction_status_id', $approvedStatusId)->sum('hold');

                    if($billStatusSlug == 'disapproved'){
                        $billNo = "-";
                    }else{
                        $billNo = "R. A. - ".($billArrayNo);
                        $billArrayNo++;
                    }
                    $records['data'][$iterator] = [
                        $billNo,
                        $basicAmount,
                        $taxAmount,
                        $finalAmount,
                        $paidAmount,
                        round(($finalAmount - $paidAmount),3),
                        $retentionAmount,
                        $tdsAmount,
                        $holdAmount,
                        $billStatus,
                        $action
                    ];
                }
                $records["draw"] = intval($request->draw);
                $records["recordsTotal"] = $iTotalRecords;
                $records["recordsFiltered"] = $iTotalRecords;
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'subcontractor bill create',
                'subcontractor_structure' => $subcontractorStructure,
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $records = array();
            $status = 500;
        }
        return response()->json($records, $status);
    }

    public function getBillView(Request $request, $subcontractorBill){
        try{
            $subcontractorStructure = $subcontractorBill->subcontractorStructure;
            $subcontractorBillTaxes = $subcontractorBill->subcontractorBillTaxes;
            $totalBills = $subcontractorStructure->subcontractorBill->sortBy('id')->pluck('id');
            $taxTotal = 0;
            $structureSlug = $subcontractorStructure->contractType->slug;
            /* No need of this 'if' block once 'itemwise' bill flow is completed. Only need code from 'else' block */
            if($subcontractorBill->qty > 0){
                if($structureSlug == 'sqft'){
                    $rate = $subcontractorStructure['rate'];
                    $subTotal = round(($subcontractorBill['qty'] * $rate),3);
                }else{
                    $rate = round(($subcontractorStructure['rate'] * $subcontractorStructure['total_work_area']),3);
                    $subTotal = round(($subcontractorBill['qty'] * $rate),3);
                }
                $BillTransactionTotals = SubcontractorBillTransaction::where('subcontractor_bills_id',$subcontractorBill->id)->sum('total');
                $totalBillHoldAmount = SubcontractorBillTransaction::where('subcontractor_bills_id',$subcontractorBill->id)->sum('hold');
                $totalBillRetentionAmount = SubcontractorBillTransaction::where('subcontractor_bills_id',$subcontractorBill->id)->sum('retention_amount');
                $paidAmount = SubcontractorBillTransaction::where('subcontractor_bills_id', $subcontractorBill->id)->sum('total');

            }else{
                $subTotal = round((($subcontractorBill->discount / 100) * $subcontractorBill->subtotal) + $subcontractorBill->subtotal, 3);
                $approvedTransactionStatusId = TransactionStatus::where('slug', 'approved')->pluck('id')->first();
                $BillTransactionTotals = SubcontractorBillTransaction::where('subcontractor_bills_id',$subcontractorBill->id)
                                                                    ->where('transaction_status_id', $approvedTransactionStatusId)
                                                                    ->sum('total');
                $totalBillHoldAmount = SubcontractorBillTransaction::where('subcontractor_bills_id',$subcontractorBill->id)
                                                                    ->where('transaction_status_id', $approvedTransactionStatusId)
                                                                    ->sum('hold');
                $totalBillRetentionAmount = SubcontractorBillTransaction::where('subcontractor_bills_id',$subcontractorBill->id)
                                                                    ->where('transaction_status_id', $approvedTransactionStatusId)
                                                                    ->sum('retention_amount');
                $paidAmount = SubcontractorBillTransaction::where('subcontractor_bills_id', $subcontractorBill->id)
                                                            ->where('transaction_status_id', $approvedTransactionStatusId)
                                                            ->sum('total');

            }
            $taxes = [];
            $specialTaxes = [];
            $iterator = 0;
            $jIterator = 0;
            foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                $isSpecial = Tax::where('id', $subcontractorBillTaxData->tax_id)->pluck('is_special')->first();
                if($isSpecial == false){
                    $taxes[$iterator] = SubcontractorBillTax::join('taxes', 'taxes.id', '=', 'subcontractor_bill_taxes.tax_id')
                                                    ->where('subcontractor_bill_taxes.id', $subcontractorBillTaxData['id'])
                                                    ->select('taxes.id as id', 'taxes.name as name', 'subcontractor_bill_taxes.percentage as percentage')
                                                    ->first()->toArray();
                    $iterator++;
                }else{
                    $specialTaxes[$jIterator] = SubcontractorBillTax::join('taxes', 'taxes.id', '=', 'subcontractor_bill_taxes.tax_id')
                        ->where('subcontractor_bill_taxes.id', $subcontractorBillTaxData['id'])
                        ->select('taxes.id as id', 'taxes.name as name', 'subcontractor_bill_taxes.percentage as percentage', 'subcontractor_bill_taxes.applied_on as applied_on')
                        ->first()->toArray();
                    $specialTaxes[$jIterator]['applied_on'] = json_decode($specialTaxes[$jIterator]['applied_on']);
                    $jIterator++;
                }
                $appliedOn = json_decode($subcontractorBillTaxData['applied_on']);
                foreach($appliedOn as $taxId){
                    if($taxId == 0 || $taxId == '0'){
                        $taxTotal += round((($subcontractorBillTaxData['percentage'] * $subTotal) / 100),3);
                    }else{
                        $appliedOnTax = SubcontractorBillTax::where('subcontractor_bills_id', $subcontractorBill->id)->where('tax_id', $taxId)->pluck('percentage')->first();
                        $appliedOnAmount = round((($appliedOnTax * $subTotal) / 100),3);
                        $taxTotal += round((($subcontractorBillTaxData['percentage'] * $appliedOnAmount) / 100),3);
                    }
                }
            }
            $appliedSpecialTaxIds = array_column($specialTaxes,'id');
            $unappliedSpecialTax = Tax::whereNotIn('id', array_column($specialTaxes, 'id'))->where('is_special', true)->select('id', 'name', 'base_percentage as percentage')->get();
            if(!$unappliedSpecialTax->isEmpty()){
                $specialTaxes += $unappliedSpecialTax->toArray();
            }
                $finalTotal = $subcontractorBill['grand_total'];
            $billStatusesToBeCountedIds = SubcontractorBillStatus::where('slug','!=', 'disapproved')->pluck('id');
            $billCount = SubcontractorBill::where('sc_structure_id', $subcontractorBill->sc_structure_id)
                ->where('created_at',  '<=', $subcontractorBill->created_at)
                ->whereIn('subcontractor_bill_status_id', $billStatusesToBeCountedIds)
                ->count('id');
            $billName = "R.A. ".($billCount);
            $noOfFloors = $totalBills->count();
            $remainingAmount = $finalTotal - $BillTransactionTotals;
            $paymentTypes = PaymentType::whereIn('slug',['cheque','neft','rtgs','internet-banking'])->orderBy('id')->get();
            $reconciledHoldAmount = SubcontractorBillReconcileTransaction::where('subcontractor_bill_id',$subcontractorBill->id)->where('transaction_slug','hold')->sum('amount');
            $remainingHoldAmount = $reconciledHoldAmount - $totalBillHoldAmount;
            $reconciledRetentionAmount = SubcontractorBillReconcileTransaction::where('subcontractor_bill_id',$subcontractorBill->id)->where('transaction_slug','retention')->sum('amount');
            $remainingRetentionAmount = $reconciledRetentionAmount - $totalBillRetentionAmount;
            $pendingAmount = $finalTotal - $paidAmount;
            $banks = BankInfo::where('is_active',true)->select('id','bank_name','balance_amount')->get();
            $statistics = $this->getSiteWiseStatistics();
            $cashAllowedLimit = ($statistics['remainingAmount'] > 0) ? $statistics['remainingAmount'] : 0 ;
            return view('subcontractor.bill.view')->with(compact('structureSlug','subcontractorBill','subcontractorStructure','noOfFloors','billName','rate','subcontractorBillTaxes','subTotal','finalTotal','remainingAmount','paymentTypes','remainingHoldAmount','remainingRetentionAmount','pendingAmount','banks','cashAllowedLimit', 'taxes', 'specialTaxes', 'appliedSpecialTaxIds'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get subcontractor bill view',
                'subcontractor_bill' => $subcontractorBill,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request, $subcontractorBill){
        try{
            $subcontractorStructureSummaries = $subcontractorBill->subcontractorStructure->summaries->toArray();
            $iterator = 0;
            foreach($subcontractorStructureSummaries as $subcontractorStructureSummary){
                $subcontractorStructureSummaries[$iterator]['summary_name'] = Summary::where('id', $subcontractorStructureSummary['summary_id'])->pluck('name')->first();
                $subcontractorStructureSummaries[$iterator]['prev_quantity'] = SubcontractorBill::join('subcontractor_structure', 'subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                    ->join('subcontractor_bill_summaries','subcontractor_bill_summaries.subcontractor_bill_id','=','subcontractor_bills.id')
                    ->where('subcontractor_bill_summaries.subcontractor_structure_summary_id', $subcontractorStructureSummary['id'])
                    ->where('subcontractor_bills.sc_structure_id', $subcontractorBill->sc_structure_id)
                    ->where('subcontractor_bills.id', '!=', $subcontractorBill->id)
                    ->sum('quantity');
                if( $subcontractorBill->subcontractorStructure->contractType->slug == 'amountwise'){
                    $subcontractorStructureSummaries[$iterator]['allowed_quantity'] = 1 - $subcontractorStructureSummaries[$iterator]['prev_quantity'];
                }else{
                    $subcontractorStructureSummaries[$iterator]['allowed_quantity'] = $subcontractorStructureSummary['total_work_area'] - $subcontractorStructureSummaries[$iterator]['prev_quantity'];
                }
                if (in_array($subcontractorStructureSummary['id'], array_column($subcontractorBill->subcontractorBillSummaries->toArray(),'subcontractor_structure_summary_id'))) {
                    $subcontractorStructureSummaries[$iterator]['description'] = $subcontractorBill->subcontractorBillSummaries->where('subcontractor_structure_summary_id', $subcontractorStructureSummary['id'])->pluck('description')->first();
                    $subcontractorStructureSummaries[$iterator]['quantity'] = $subcontractorBill->subcontractorBillSummaries->where('subcontractor_structure_summary_id', $subcontractorStructureSummary['id'])->pluck('quantity')->first();
                    $subcontractorStructureSummaries[$iterator]['is_bill_created'] = true;
                } else {
                    $subcontractorStructureSummaries[$iterator]['description'] = '';
                    $subcontractorStructureSummaries[$iterator]['quantity'] = 0;
                    $subcontractorStructureSummaries[$iterator]['is_bill_created'] = false;
                }
                $iterator += 1;
            }
            $structureExtraItems = SubcontractorStructureExtraItem::join('extra_items', 'extra_items.id', '=', 'subcontractor_structure_extra_items.extra_item_id')
                ->where('subcontractor_structure_extra_items.subcontractor_structure_id', $subcontractorBill->subcontractorStructure->id)
                ->select('subcontractor_structure_extra_items.id as subcontractor_structure_extra_item_id', 'subcontractor_structure_extra_items.rate as rate','extra_items.name as name')
                ->get()->toArray();
            $billStatusesToBeCountedIds = SubcontractorBillStatus::where('slug','!=', 'disapproved')->pluck('id');
            $billCount = SubcontractorBill::where('sc_structure_id', $subcontractorBill->sc_structure_id)
                    ->where('created_at',  '<=', $subcontractorBill->created_at)
                    ->whereIn('subcontractor_bill_status_id', $billStatusesToBeCountedIds)
                    ->count('id');
            $billName = "R.A. ".($billCount);
            $taxes = SubcontractorBillTax::join('taxes', 'taxes.id', '=', 'subcontractor_bill_taxes.tax_id')
                                        ->where('subcontractor_bill_taxes.subcontractor_bills_id', $subcontractorBill->id)
                                        ->where('taxes.is_special', false)
                                        ->select('taxes.id as id', 'taxes.name as name', 'subcontractor_bill_taxes.percentage as percentage')
                                        ->get();
            $specialTaxes = Tax::where('is_active', true)->where('is_special', true)->select('id', 'name', 'base_percentage')->get();

            $billAppliedSpecialTaxes = SubcontractorBillTax::join('taxes', 'taxes.id', '=', 'subcontractor_bill_taxes.tax_id')
                                                        ->where('subcontractor_bill_taxes.subcontractor_bills_id', $subcontractorBill->id)
                                                        ->where('taxes.is_special', true)
                                                        ->select('taxes.id as id', 'subcontractor_bill_taxes.percentage as percentage','subcontractor_bill_taxes.applied_on as applied_on')
                                                        ->get();
            $appliedSpecialTaxes = [];
            foreach($billAppliedSpecialTaxes as $appliedSpecialTax){
                $appliedSpecialTaxes[$appliedSpecialTax['id']] = $appliedSpecialTax->toArray();
            }
            return view('subcontractor.bill.edit')->with(compact('subcontractorBill', 'subcontractorStructureSummaries', 'structureExtraItems', 'taxes', 'billName', 'specialTaxes', 'appliedSpecialTaxes'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get subcontractor bill edit view',
                'subcontractor_bill' => $subcontractorBill,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function changeBillStatus(Request $request, $statusSlug, $subcontractorBill){
        try{
            $subcontractorBill->update([
                'subcontractor_bill_status_id' => SubcontractorBillStatus::where('slug',$statusSlug)->pluck('id')->first()
            ]);
            $request->session()->flash('success', 'Bill Status changed successfully.');
            return redirect('/subcontractor/bill/view/'.$subcontractorBill->id);
        } catch (\Exception $e){
            $data = [
                'action' => 'Change subcontractor bill status',
                'subcontractor_bill' => $subcontractorBill,
                'status_slug' => $statusSlug,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editBill(Request $request, $subcontractorBill){
        try{
            $subcontractorBillData = $request->only('discount', 'discount_description', 'subtotal', 'round_off_amount', 'grand_total');
            $approvedTrasanctionStatusId = TransactionStatus::where('slug','approved')->pluck('id')->first();
            $subcontractorTransactionAmount = $subcontractorBill->subcontractorBillTransaction
                ->where('transaction_status_id',$approvedTrasanctionStatusId)->sum('total');
            if($subcontractorTransactionAmount > $subcontractorBillData['grand_total']){
                $request->session()->flash('error', 'Cannot Edit the bill as Transaction amount is greater than the Bill amount you edited.');
                return redirect('/subcontractor/bill/view/'.$subcontractorBill->id);
            }
            if($request->has('performa_invoice_date') && $request->performa_invoice_date != '' && $request->performa_invoice_date != null){
                $subcontractorBillData['performa_invoice_date'] = date('Y-m-d', strtotime($request->performa_invoice_date));
            }
            if($request->has('bill_date') && $request->bill_date != '' && $request->bill_date != null){
                $subcontractorBillData['bill_date'] = date('Y-m-d', strtotime($request->bill_date));
            }
            $subcontractorBill->update($subcontractorBillData);
            foreach($request->structure_summaries as $structureSummaryId){
                $subcontractorBillSummaryData = [
                    'subcontractor_bill_id' => $subcontractorBill->id,
                    'subcontractor_structure_summary_id' => $structureSummaryId
                ];
                $subcontractorBillSummary = SubcontractorBillSummary::where($subcontractorBillSummaryData)->first();
                $subcontractorBillSummaryData['quantity'] = $request->quantity[$structureSummaryId];
                $subcontractorBillSummaryData['description'] = $request->description[$structureSummaryId];
                $subcontractorBillSummaryData['total_work_area'] = $request->total_work_area[$structureSummaryId];
                if($subcontractorBillSummary == null){
                    $subcontractorBillSummary = SubcontractorBillSummary::create($subcontractorBillSummaryData);
                }else{
                    $subcontractorBillSummary->update($subcontractorBillSummaryData);
                }
            }
            if($request->has('structure_extra_item_ids')){
                foreach($request->structure_extra_item_ids as $structureExtraItemId){
                    $subcontractorBillExtraItemData = [
                        'subcontractor_bill_id' => $subcontractorBill->id,
                        'subcontractor_structure_extra_item_id' => $structureExtraItemId
                    ];
                    $subcontractorBillExtraItem = SubcontractorBillExtraItem::where($subcontractorBillExtraItemData)->first();
                    $subcontractorBillExtraItemData['subcontractor_structure_extra_item_id'] = $structureExtraItemId;
                    $subcontractorBillExtraItemData['rate'] = $request->structure_extra_item_rate[$structureExtraItemId];
                    $subcontractorBillExtraItemData['description'] = $request->structure_extra_item_description[$structureExtraItemId];
                    if($subcontractorBillExtraItem == null){
                        $subcontractorBillExtraItem = SubcontractorBillExtraItem::create($subcontractorBillExtraItemData);
                    } else {
                        $subcontractorBillExtraItem->update($subcontractorBillExtraItemData);
                    }
                }
            }
            if($request->has('taxes')){
                foreach($request->taxes as $taxId => $percentage){
                    $subcontractorBillTaxData = [
                        'subcontractor_bills_id' => $subcontractorBill->id,
                        'tax_id' => $taxId
                    ];
                    $subcontractorBillTax = SubcontractorBillTax::where($subcontractorBillTaxData)->first();
                    $subcontractorBillTaxData['percentage'] = $percentage;
                    $subcontractorBillTaxData['applied_on'] = json_encode([0]);
                    if($subcontractorBillTax == null){
                        $subcontractorBillTax = SubcontractorBillTax::create($subcontractorBillTaxData);
                    } else {
                        $subcontractorBillTax->update($subcontractorBillTaxData);
                    }
                }
            }
            if($request->has('applied_on')){
                foreach ($request->applied_on as $specialTaxId => $specialTaxData){
                    if(array_key_exists('on', $specialTaxData)){
                        $subcontractorBillTaxData = [
                            'subcontractor_bills_id' => $subcontractorBill->id,
                            'tax_id' => $specialTaxId
                        ];
                        $subcontractorBillTax = SubcontractorBillTax::where($subcontractorBillTaxData)->first();
                        $subcontractorBillTaxData['percentage'] = $specialTaxData['percentage'];
                        $subcontractorBillTaxData['applied_on'] = [];
                        foreach($specialTaxData['on'] as $taxId){
                            $subcontractorBillTaxData['applied_on'][] = $taxId;
                        }
                        $subcontractorBillTaxData['applied_on'] = json_encode($subcontractorBillTaxData['applied_on']);
                        if ($subcontractorBillTax == null){
                            $subcontractorBillTax = SubcontractorBillTax::create($subcontractorBillTaxData);
                        }else{
                            $subcontractorBillTax->update($subcontractorBillTaxData);
                        }
                    }else{
                        SubcontractorBillTax::where('subcontractor_bills_id', $subcontractorBill->id)->where('tax_id', $specialTaxId)->delete();
                    }
                }
            }
            $request->session()->flash('success', 'Subcontractor Bill edited successfully.');
            return redirect('/subcontractor/bill/view/'.$subcontractorBill->id);
        } catch (\Exception $e){
            $data = [
                'action' => 'Change subcontractor bill status',
                'subcontractor_bill' => $subcontractorBill,
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createTransaction(Request $request){
        try{
            $subcontractorBillTransactionData = $request->except('_token','remainingTotal','bank_id','payment_id','paid_from_slug');
            $approvedBillStatusId = TransactionStatus::where('slug','approved')->pluck('id')->first();
            $subcontractorBillTransactionData['transaction_status_id'] = $approvedBillStatusId;
            $subcontractorBill = SubcontractorBill::where('id',$request['subcontractor_bills_id'])->first();
            if($request->has('is_advance')){
                $subcontractorBillTransactionData['is_advance'] = true;
                $subcontractorBillTransaction = SubcontractorBillTransaction::create($subcontractorBillTransactionData);
                $subcontractor = $subcontractorBillTransaction->subcontractorBill->subcontractorStructure->subcontractor;
                $balanceAdvanceAmount = $subcontractor->balance_advance_amount;
                $subcontractor->update(['balance_advance_amount' => $balanceAdvanceAmount - $subcontractorBillTransaction->total]);
            }elseif($request['paid_from_slug'] == 'bank'){
                $bank = BankInfo::where('id',$request['bank_id'])->first();
                if($request['total'] <= $bank['balance_amount']){
                    $subcontractorBillTransactionData['is_advance'] = false;
                    $subcontractorBillTransactionData['bank_id'] = $request['bank_id'];
                    $subcontractorBillTransactionData['payment_type_id'] = $request['payment_id'];
                    $subcontractorBillTransactionData['paid_from_slug'] = $request['paid_from_slug'];
                    $subcontractorBillTransaction = SubcontractorBillTransaction::create($subcontractorBillTransactionData);
                    $bankData['balance_amount'] = $bank['balance_amount'] - $subcontractorBillTransaction['subtotal'];
                    $bank->update($bankData);
                }else{
                    $request->session()->flash('success','Bank Balance Amount is insufficient for this transaction');
                    return redirect('/subcontractor/bill/view/'.$subcontractorBill->id);
                }
            }elseif ($request['paid_from_slug'] == 'cancel_transaction_advance'){
                $subcontractorStructure = $subcontractorBill->subcontractorStructure;
                if($request['total'] <= $subcontractorStructure['cancelled_bill_transaction_balance_amount']){
                    $subcontractorBillTransactionData['is_advance'] = false;
                    $subcontractorBillTransactionData['payment_type_id'] = $request['payment_id'];
                    $subcontractorBillTransactionData['paid_from_slug'] = $request['paid_from_slug'];
                    $subcontractorBillTransaction = SubcontractorBillTransaction::create($subcontractorBillTransactionData);
                    $subcontractorStructure->update([
                        'cancelled_bill_transaction_balance_amount' => $subcontractorStructure['cancelled_bill_transaction_balance_amount'] - $request->amount
                    ]);
                }else{
                    $request->session()->flash('error','Cancel Bill Transaction amount is insufficient for this transaction');
                    return redirect('/subcontractor/bill/view/'.$subcontractorBill->id);
                }
            }else{
                $statistics = $this->getSiteWiseStatistics();
                $cashAllowedLimit = ($statistics['remainingAmount'] > 0) ? $statistics['remainingAmount'] : 0 ;
                if($request['total'] <= $cashAllowedLimit){
                    $subcontractorBillTransactionData['is_advance'] = false;
                    $subcontractorBillTransactionData['paid_from_slug'] = $request['paid_from_slug'];
                    $subcontractorBillTransaction = SubcontractorBillTransaction::create($subcontractorBillTransactionData);
                }else{
                    $request->session()->flash('success','Cash Amount is insufficient for this transaction');
                    return redirect('/subcontractor/bill/view/'.$subcontractorBill->id);
                }
            }

            if($subcontractorBillTransaction != null){
                $request->session()->flash('success','Transaction created successfully');
            }else{
                $request->session()->flash('error','Cannot create transaction');
            }
            return redirect('/subcontractor/bill/view/'.$subcontractorBill->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Subcontractor Bill Transaction',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::Critical(json_encode($data));
            abort(500);
        }
    }

    public function changeBillTransactionStatus(Request $request){
        try{
            $subcontractorBillTransaction = new SubcontractorBillTransaction();
            $transactionStatus = new TransactionStatus();
            $billTransactionData = $subcontractorBillTransaction->where('id',$request['bill_transaction_id'])->first();
            $bill = $billTransactionData->subcontractorBill;

            $subcontractorStructure = $bill->subcontractorStructure;
            if($request['status-slug'] == 'cancelled'){
                $subcontractorStructure->update([
                    'cancelled_bill_transaction_total_amount' => $subcontractorStructure['cancelled_bill_transaction_total_amount'] + $billTransactionData['total'],
                    'cancelled_bill_transaction_balance_amount' => $subcontractorStructure['cancelled_bill_transaction_balance_amount'] + $billTransactionData['total']
                ]);
            }elseif($billTransactionData->transactionStatus->slug == 'cancelled'){
                $subcontractorStructure->update([
                    'cancelled_bill_transaction_total_amount' => $subcontractorStructure['cancelled_bill_transaction_total_amount'] - $billTransactionData['total'],
                    'cancelled_bill_transaction_balance_amount' => $subcontractorStructure['cancelled_bill_transaction_balance_amount'] - $billTransactionData['total']
                ]);
            }
            $billTransactionData->update([
                'transaction_status_id' => $transactionStatus->where('slug',$request['status-slug'])->pluck('id')->first(),
                'remark' => $request['remark']
            ]);

            return redirect('subcontractor/bill/view/'.$bill->id);
        }catch (\Exception $e){
            $status = 500;
            $data = [
                'action' => 'Change Transaction status',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }
}
