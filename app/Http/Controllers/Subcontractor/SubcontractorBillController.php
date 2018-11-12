<?php

namespace App\Http\Controllers\Subcontractor;

use App\SubcontractorBill;
use App\SubcontractorBillExtraItem;
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

                $subcontractorStructureSummaries[$iterator]['allowed_quantity'] = $subcontractorStructureSummary['total_work_area'] - $subcontractorStructureSummaries[$iterator]['prev_quantity'];
                $iterator += 1;
            }
            $structureExtraItems = SubcontractorStructureExtraItem::join('extra_items', 'extra_items.id', '=', 'subcontractor_structure_extra_items.extra_item_id')
                                                    ->where('subcontractor_structure_extra_items.subcontractor_structure_id', $subcontractorStructure->id)
                                                    ->select('subcontractor_structure_extra_items.id as subcontractor_structure_extra_item_id', 'subcontractor_structure_extra_items.rate as rate','extra_items.name as name')
                                                    ->get()->toArray();
            $totalBillCount = $subcontractorStructure->subcontractorBill->count();
            $billName = "R.A. ".($totalBillCount + 1);
            $taxes = Tax::whereNotIn('slug',['vat'])->where('is_active',true)->where('is_special',false)->select('id','name','slug','base_percentage')->get();
            return view('subcontractor.bill.create')->with(compact('billName', 'taxes', 'subcontractorStructure', 'subcontractorStructureSummaries', 'structureExtraItems'));
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
                    $subcontractorBillTax = SubcontractorBillTax::create($subcontractorBillTaxData);
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
                    $structureTypeSlug = SubcontractorStructureType::where('id',$data['sc_structure_type_id'])->pluck('slug')->first();
                    if($data['qty'] > 0){
                        if($structureTypeSlug == 'sqft' || $structureTypeSlug == 'itemwise'){
                            $rate = $data['rate'];
                            $basicAmount = round(($rate * $data['qty']),3);
                        }else{
                            $rate = $data['rate'] * $data['total_work_area'];
                            $basicAmount = round(($rate * $data['qty']),3);
                        }
                        $taxesApplied = SubcontractorBillTax::where('subcontractor_bills_id',$data['id'])->sum('percentage');
                        $taxAmount = round(($basicAmount * ($taxesApplied / 100)),3);
                        $finalAmount += round(($basicAmount + $taxAmount),3);
                        $paidAmount += SubcontractorBillTransaction::where('subcontractor_bills_id', $data['id'])->sum('total');
                    }else{
                        $finalAmount += $data['grand_total'];
                        $approvedStatusId = TransactionStatus::where('slug', 'approved')->pluck('id')->first();
                        $paidAmount += SubcontractorBillTransaction::where('subcontractor_bills_id', $data['id'])
                            ->where('transaction_status_id', $approvedStatusId)
                            ->sum('total');
                    }
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
                    $action = '<div class="btn btn-xs green">
                        <a href="javascript:void(0);" style="color: white">
                             View Bill
                        </a>
                    </div>';
                    $billStatus = $listingData[$pagination]->subcontractorBillStatus->name;
                    $structureTypeSlug = SubcontractorStructureType::where('id',$listingData[$pagination]['sc_structure_type_id'])->pluck('slug')->first();
                    if($listingData[$pagination]['qty'] > 0){
                        if($structureTypeSlug == 'sqft' || $structureTypeSlug == 'itemwise'){
                            $rate = $listingData[$pagination]['rate'];
                            $basicAmount = round(($rate * $listingData[$pagination]['qty']),3);
                        }else{
                            $rate = $listingData[$pagination]['rate'] * $listingData[$pagination]['total_work_area'];
                            $basicAmount = round(($rate * $listingData[$pagination]['qty']),3);
                        }
                        $taxesApplied = SubcontractorBillTax::where('subcontractor_bills_id',$listingData[$pagination]['id'])->sum('percentage');
                        $taxAmount = round(($basicAmount * ($taxesApplied / 100)),3);
                        $finalAmount = round(($basicAmount + $taxAmount),3);
                        $paidAmount = SubcontractorBillTransaction::where('subcontractor_bills_id', $listingData[$pagination]['id'])->sum('total');
                    }else{
                        $basicAmount = round((($listingData[$pagination]->discount / 100) * $listingData[$pagination]->subtotal) + $listingData[$pagination]->subtotal, 3);
                        $taxAmount = round(($listingData[$pagination]['grand_total'] - $basicAmount - $listingData[$pagination]['round_off_amount']), 3);
                        $finalAmount = $listingData[$pagination]['grand_total'];
                        $approvedStatusId = TransactionStatus::where('slug', 'approved')->pluck('id')->first();
                        $paidAmount = SubcontractorBillTransaction::where('subcontractor_bills_id', $listingData[$pagination]['id'])
                                                                    ->where('transaction_status_id', $approvedStatusId)
                                                                    ->sum('total');
                    }
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
}
