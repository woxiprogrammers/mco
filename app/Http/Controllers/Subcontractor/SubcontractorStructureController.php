<?php

namespace App\Http\Controllers\Subcontractor;

use App\ExtraItem;
use App\Project;
use App\Subcontractor;
use App\SubcontractorBill;
use App\SubcontractorBillExtraItem;
use App\SubcontractorBillStatus;
use App\SubcontractorBillSummary;
use App\SubcontractorBillTransaction;
use App\SubcontractorStructure;
use App\SubcontractorStructureExtraItem;
use App\SubcontractorStructureSummary;
use App\SubcontractorStructureType;
use App\Summary;
use App\TransactionStatus;
use App\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubcontractorCashEditRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SubcontractorStructureController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request){
        try{
            $contractTypes = SubcontractorStructureType::select('id', 'name')->get()->toArray();
            return view('subcontractor.new_structure.manage')->with(compact('contractTypes'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Subcontractor structure manage view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request){
        try{
            $units = Unit::where('is_active', true)->select('id', 'name')->get()->toArray();
            $subcontractors = Subcontractor::where('is_active',true)->orderBy('subcontractor_name','asc')->get(['id','subcontractor_name'])->toArray();
            $ScStrutureTypes = SubcontractorStructureType::orderBy('id','asc')->get(['id','name','slug'])->toArray();
            $summaries = Summary::where('is_active', true)->select('id', 'name')->get()->toArray();
            $extraItems = ExtraItem::where('is_active', true)->select('id','name','rate')->orderBy('name','asc')->get();
            return view('subcontractor.new_structure.create')->with(compact('subcontractors', 'ScStrutureTypes', 'summaries', 'extraItems', 'units'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get subcontractor structure create view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createStructure(Request $request){
        try{
            $subcontractorStructureData = [
                'subcontractor_id' => (int)$request->subcontractor_id,
                'sc_structure_type_id' => SubcontractorStructureType::where('slug', $request->structure_type)->pluck('id')->first(),
            ];
            if($projectSiteId = Session::get('global_project_site')){
                $subcontractorStructureData['project_site_id'] = (int) $projectSiteId;
            } elseif ($projectSiteId = $request->project_site_id){
                $subcontractorStructureData['project_site_id'] = (int) $projectSiteId;
            } else {
                $request->session()->flash('error', 'Project site is not selected');
                return redirect('/subcontractor/structure/create');
            }
            $subcontractorStructure = SubcontractorStructure::create($subcontractorStructureData);
            $structureSummaryData = [
                'subcontractor_structure_id' => $subcontractorStructure->id
            ];
            $counter = 0;
            foreach($request->summaries as $summaryId){
                $structureSummaryData['summary_id'] = (int) $summaryId;
                $structureSummaryData['rate'] = (float) $request->rate[$counter][$summaryId];
                $structureSummaryData['description'] = $request->description[$counter][$summaryId];
                $structureSummaryData['total_work_area'] = (float)$request->total_work_area[$counter][$summaryId];
                $structureSummaryData['unit_id'] = (int)$request->unit[$counter][$summaryId];
                $structureSummary = SubcontractorStructureSummary::create($structureSummaryData);
                $counter++;
            }
            $structureExtraItemData = [
                'subcontractor_structure_id' => $subcontractorStructure->id
            ];
            if($request->has('extra_items')){
                foreach($request->extra_items as $extraItemId => $rate){
                    $structureExtraItemData['extra_item_id'] = $extraItemId;
                    $structureExtraItemData['rate'] = (double)$rate;
                    $structureExtraItem = SubcontractorStructureExtraItem::create($structureExtraItemData);
                }
            }

            if($request->has('new_extra_item')){
                foreach($request->new_extra_item as $extraItemData){
                    $extraItemData['is_active'] = false;
                    $extraItem = ExtraItem::create($extraItemData);
                    $structureExtraItemData['extra_item_id'] = $extraItem->id;
                    $structureExtraItemData['rate'] = (double)$extraItem->rate;
                    $structureExtraItem = SubcontractorStructureExtraItem::create($structureExtraItemData);
                }
            }
            $request->session()->flash('success', 'Subcontractor structure created successfully.');
            return redirect('/subcontractor/structure/manage');
        }catch (\Exception $e){
            $data = [
                'action' => 'subcontractor structure create',
                'params' => json_encode($request->all()),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function structureListing(Request $request){
        try{
            $skip = $request->start;
            $take = $request->length;
            $totalRecordCount = 0;
            $user = Auth::user();
            $filterFlag = true;
            $subcontractor_name = null;
            $project_name = null;
            $ids = SubcontractorStructure::pluck('id');
            $approvedStatusId = TransactionStatus::where('slug', 'approved')->pluck('id')->first();
            if($request->has('project_name') && $filterFlag == true){
                $projectSites = Project::join('project_sites','project_sites.project_id','=','projects.id')->where('projects.name','ilike','%'.$request['project_name'].'%')->select('project_sites.id')->get()->toArray();
                $ids = SubcontractorStructure::where('project_site_id','!=', 0)
                    ->whereIn('id',$ids)
                    ->whereIn('project_site_id',$projectSites)
                    ->orderBy('created_at','desc')->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if($request->has('subcontractor_name') && $filterFlag == true){
                $subContractorid = Subcontractor::where('company_name','ilike','%'.$request['subcontractor_name'].'%')->select('id')->get()->toArray();
                $ids = SubcontractorStructure::whereIn('subcontractor_id',$subContractorid)
                    ->whereIn('id',$ids)->orderBy('created_at','desc')->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if($request->has('contract_type_id') && $request->contract_type_id != "" && $filterFlag == true){
                $ids = SubcontractorStructure::whereIn('id',$ids)
                                    ->where('sc_structure_type_id', $request->contract_type_id)
                                    ->orderBy('created_at','desc')->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            $listingData = array();
            if ($filterFlag) {
                $listingData = SubcontractorStructure::whereIn('id',$ids)->orderBy('id', 'desc')
                                ->skip($skip)->take($take)->get();
                $totalRecordCount = SubcontractorStructure::whereIn('id',$ids)->count();
            }
            if ($request->has('get_total')) {
                $billTotals = 0;
                $billPaidAmount = 0;
                $totalRate = 0;
                $totalWorkArea = 0;
                $totalAmount = 0;
                if ($filterFlag) {
                    foreach($listingData as $subcontractorStruct) {
                        $totalRate += array_sum(array_column($subcontractorStruct->summaries->toArray(),'rate'));
                        $totalWorkArea += array_sum(array_column($subcontractorStruct->summaries->toArray(),'rate'));;
                        $totalAmount += $subcontractorStruct->summaries->sum(function($summary){
                            return $summary->rate * $summary->total_work_area;
                        });
                        $subcontractorBillIdsArray = $subcontractorStruct->subcontractorBill->where('subcontractor_bill_status_id',SubcontractorBillStatus::where('slug','approved')->pluck('id')->first())->pluck('id');
                        foreach ($subcontractorBillIdsArray as $subBillids) {
                            $subcontractorBill = SubcontractorBill::where('id',$subBillids)->first();
                            $billTotals += round(($subcontractorBill['grand_total']),3);
                            $billPaidAmount += round((SubcontractorBillTransaction::where('transaction_status_id', $approvedStatusId)->where('subcontractor_bills_id',$subBillids)->sum('total')),3);
                        }
                    }
                }
                $records['totalRate'] = round($totalRate,3);
                $records['totalWorkArea'] = round($totalWorkArea,3);
                $records['totalAmount'] = round($totalAmount,3);
                $records['billtotal'] = round($billTotals,3);
                $records['paidtotal'] = round($billPaidAmount,3);
                $records['balancetotal'] = round(($billTotals - $billPaidAmount),3);
            } else {
                $iTotalRecords = count($listingData);
                $records = array();
                $records['data'] = array();
                $end = $request->length < 0 ? count($listingData) : $request->length;
                for ($iterator = 0, $pagination = 0; $iterator < $end && $pagination < count($listingData); $iterator++, $pagination++) {
                    $subcontractorBillIds = $listingData[$pagination]->subcontractorBill->where('subcontractor_bill_status_id', SubcontractorBillStatus::where('slug', 'approved')->pluck('id')->first())->pluck('id');
                    $billTotals = 0;
                    $billPaidAmount = 0;
                    foreach ($subcontractorBillIds as $subcontractorStructureBillId) {
                        $subcontractorBill = SubcontractorBill::where('id', $subcontractorStructureBillId)->first();
                        $billTotals += round($subcontractorBill['grand_total'], 3);
                        $billPaidAmount += round((SubcontractorBillTransaction::where('transaction_status_id', $approvedStatusId)->where('subcontractor_bills_id', $subcontractorStructureBillId)->sum('total')), 3);
                    }
                    $action = '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">';
                    if ($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-subcontractor-structure') || $user->customHasPermission('view-subcontractor-structure')) {
                        $action .= '<li>
                                        <a href="/subcontractor/structure/edit/'.$listingData[$pagination]->id.'">
                                             <i class="icon-docs"></i>Edit
                                        </a>
                                     </li>
                                     <li>
                                        <a href="javascript:void(0);" onclick="getSummaries('.$listingData[$pagination]->id.')">
                                             <i class="icon-docs"></i>View
                                        </a>
                                     </li>';
                    }
                    if ($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-subcontractor-billing') || $user->customHasPermission('edit-subcontractor-billing') || $user->customHasPermission('view-subcontractor-billing') || $user->customHasPermission('approve-subcontractor-billing')) {
                        $action .= '<li><a href="/subcontractor/bill/manage/'.$listingData[$pagination]->id.'">
                                            <i class="icon-docs"></i> Manage
                                        </a></li>';
                    }
                    $action .= '</ul>
                        </div>';
                    $totalRate = array_sum(array_column($listingData[$pagination]->summaries->toArray(),'rate'));
                    $totalWorkArea = array_sum(array_column($listingData[$pagination]->summaries->toArray(),'total_work_area'));;
                    $totalAmount = $listingData[$pagination]->summaries->sum(function($summary){
                        return $summary->rate * $summary->total_work_area;
                    });
                    $records['data'][$iterator] = [
                        ucwords($listingData[$pagination]->subcontractor->subcontractor_name),
                        $listingData[$pagination]->projectSite->project->name,
                        $listingData[$pagination]->contractType->name,
                        $totalRate,
                        $totalWorkArea,
                        $totalAmount,
                        $billTotals,
                        $billPaidAmount,
                        round(($billTotals - $billPaidAmount), 3),
                        date('d M Y', strtotime($listingData[$pagination]['created_at'])),
                        $action
                    ];
                }
                $records["draw"] = intval($request->draw);
                $records["recordsTotal"] = $totalRecordCount;
                $records["recordsFiltered"] = $totalRecordCount;
            }
            $status = 200;
        }catch (\Exception $e){
            $records = [];
            $status = 500;
            $data = [
                'action' => 'subcontractor structure listing',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        return response()->json($records, $status);
    }

    public function getStructureDetails(Request $request){
        try{
            $subcontractorStructure = SubcontractorStructure::findOrFail($request->subcontractor_structure_id);
            return view('subcontractor.new_structure.details_partial')->with(compact('subcontractorStructure'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get subcontractor structure Detail view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json(['message' => 'Something went wrong.'], 500);
        }
    }

    public function getEditView(Request $request, $subcontractorStructure){
        try{
            $summaries = Summary::where('is_active', true)->select('id', 'name')->get()->toArray();
            $structureSummaries = $subcontractorStructure->summaries->except(['created_at','updated_at'])->toArray();
            $iterator = 0;
            $approvedTransactionStatus = TransactionStatus::where('slug', 'active')->pluck('id')->first();
            foreach($structureSummaries as $structureSummary){
                $structureSummaries[$iterator]['summary_name'] = Summary::where('id', $structureSummary['summary_id'])->pluck('name')->first();
                $subcontractorBillSummaries = SubcontractorBillSummary::where('subcontractor_structure_summary_id', $structureSummary['id'])->get();
                if ($subcontractorBillSummaries->isEmpty()){
                    $structureSummaries[$iterator]['min_rate'] = 1;
                    $structureSummaries[$iterator]['min_total_work_area'] = 1;
                    if (in_array($subcontractorStructure->contractType->slug, ['itemwise', 'amountwise'])){
                        $structureSummaries[$iterator]['can_remove'] = true;
                    }else{
                        $structureSummaries[$iterator]['can_remove'] = false;
                    }
                } else {
                    $approvedBillTransactions = SubcontractorBillTransaction::join('subcontractor_bills', 'subcontractor_bills.id','=', 'subcontractor_bill_transactions.subcontractor_bills_id')
                        ->where('subcontractor_bill_transactions.transaction_status_id', $approvedTransactionStatus)
                        ->where('subcontractor_bills.sc_structure_id', $subcontractorStructure->id )
                        ->get();
                    if ($approvedBillTransactions->isEmpty()){
                        $structureSummaries[$iterator]['min_rate'] = 1;
                        $structureSummaries[$iterator]['min_total_work_area'] = 1;
                        $structureSummaries[$iterator]['can_remove'] = true;
                    } else {
                        $structureSummaries[$iterator]['min_rate'] = $structureSummary->rate;
                        $structureSummaries[$iterator]['min_total_work_area'] = $structureSummary->total_work_area;
                        $structureSummaries[$iterator]['can_remove'] = false;
                    }
                    if($subcontractorStructure->contractType->slug == 'sqft' && $structureSummaries[$iterator]['can_remove']){
                        $structureSummaries[$iterator]['can_remove'] = false;
                    }

                }
                $iterator += 1;
            }
            $structureExtraItemIds = array_column($subcontractorStructure->extraItems->toArray(), 'extra_item_id');
            $newExtraItems = ExtraItem::whereNotIn('id', $structureExtraItemIds)->where('is_active', true)->select('id','name','rate')->get()->toArray();
            $units = Unit::where('is_active', true)->select('id', 'name')->get()->toArray();
            return view('subcontractor.new_structure.edit')->with(compact('subcontractorStructure', 'summaries', 'structureSummaries', 'newExtraItems', 'units'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get subcontractor structure Edit view',
                'subcontractor_structure' => $subcontractorStructure,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editStructure(Request $request, $subcontractorStructure){
        try{
            $structureSummaryIds = [];
            foreach($request->structure_summaries as $structureSummary){
                if(array_key_exists('summary_id', $structureSummary)){
                    $structureSummary['subcontractor_structure_id'] = $subcontractorStructure->id;
                    $subcontractorStructureSummary = SubcontractorStructureSummary::create($structureSummary);
                } elseif (array_key_exists('subcontractor_structure_summary_id', $structureSummary)) {
                    $subcontractorStructureSummary = SubcontractorStructureSummary::where('id', $structureSummary['subcontractor_structure_summary_id'])->first();
                    unset($structureSummary['subcontractor_structure_summary_id']);
                    $subcontractorStructureSummary->update($structureSummary);
                } else {
                    $request->session()->flash('error', 'Something went wrong with submitted data.');
                    return redirect('/subcontractor/structure/edit/'.$subcontractorStructure->id);
                }
                $structureSummaryIds[] = $subcontractorStructureSummary->id;
            }
            $deletedStructureSummaries = SubcontractorStructureSummary::where('subcontractor_structure_id', $subcontractorStructure->id)->whereNotIn('id', $structureSummaryIds)->get();
            if($deletedStructureSummaries->isNotEmpty()){
                foreach ($deletedStructureSummaries as $deletedStructureSummary){
                    $deletedStructureSummary->delete();
                }
            }
            if($request->has('extra_items')){
                foreach($request->extra_items as $extraItemId => $rate){
                    $structureExtraItemData = [
                        'subcontractor_structure_id' => $subcontractorStructure->id,
                        'extra_item_id' => $extraItemId
                    ];
                    $structureExtraItem = SubcontractorStructureExtraItem::where($structureExtraItemData)->first();
                    $structureExtraItemData['rate'] = (double)$rate;
                    if ($structureExtraItem == null){
                        $structureExtraItem = SubcontractorStructureExtraItem::create($structureExtraItemData);
                    }else{
                        $structureExtraItem->update($structureExtraItemData);
                    }
                }
            }
            if($request->has('new_extra_item')){
                $structureExtraItemData = [
                    'subcontractor_structure_id' => $subcontractorStructure->id
                ];
                foreach($request->new_extra_item as $extraItemData){
                    $extraItemData['is_active'] = false;
                    $extraItem = ExtraItem::create($extraItemData);
                    $structureExtraItemData['extra_item_id'] = $extraItem->id;
                    $structureExtraItemData['rate'] = (double)$extraItem->rate;
                    $structureExtraItem = SubcontractorStructureExtraItem::create($structureExtraItemData);
                }
            }
            $request->session()->flash('success', 'Subcontractor structure edited successfully.');
            return redirect('/subcontractor/structure/edit/'.$subcontractorStructure->id);
        }catch (\Exception $e){
            $data = [
                'action' => 'Edit subcontractor structure',
                'subcontractor_structure' => $subcontractorStructure,
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function deleteExtraItem(Request $request,$id,$structureId){
        try{
            $billStructure = SubcontractorBill::where('sc_structure_id',$structureId)->pluck('id');
            $flag= true;
            $billStructureExtraItems = SubcontractorBillExtraItem::whereIn('subcontractor_bill_id',$billStructure)->distinct()->pluck('subcontractor_structure_extra_item_id');
            foreach ($billStructureExtraItems as $extraItem){
               $structBill = SubcontractorStructureExtraItem::where('id',$extraItem)->value('extra_item_id');
                if($id == $structBill){
                    $flag = false;
                }
            }
            if($flag) {
                $data['status'] = true;
                $data['message'] = "Extra Item Deleted Successfully";
                $query = SubcontractorStructureExtraItem::where('subcontractor_structure_id',$structureId)->where('extra_item_id',$id)->delete();
            } else {
                $data['status'] = false;
                $data['message'] = "Cannot Delete. Extra item already assigned to bill";
            }
            return $data;
        }catch (\Exception $e){
            $data = [
                'action' => 'delete Extra Item',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function cashentryManage(Request $request){
        try{
            $contractTypes = SubcontractorStructureType::select('id', 'name')->get()->toArray();
            return view('subcontractor.new_structure.manage-cash-entries')->with(compact('contractTypes'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get Subcontractor structure manage view',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function cashEntryListing(Request $request){
        try{
            $skip = $request->start;
            $take = $request->length;
            $totalRecordCount = 0;
            $user = Auth::user();
            $filterFlag = true;
            $subcontractor_name = null;
            $project_name = null;
            $ids = SubcontractorStructure::pluck('id');
            
            if($request->has('project_name') && $filterFlag == true){
                $projectSites = Project::join('project_sites','project_sites.project_id','=','projects.id')->where('projects.name','ilike','%'.$request['project_name'].'%')->select('project_sites.id')->get()->toArray();
                $ids = SubcontractorStructure::where('project_site_id','!=', 0)
                    ->whereIn('id',$ids)
                    ->whereIn('project_site_id',$projectSites)
                    ->orderBy('created_at','desc')->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if($request->has('subcontractor_name') && $filterFlag == true){
                $subContractorid = Subcontractor::where('company_name','ilike','%'.$request['subcontractor_name'].'%')->select('id')->get()->toArray();
                $ids = SubcontractorStructure::whereIn('subcontractor_id',$subContractorid)
                    ->whereIn('id',$ids)->orderBy('created_at','desc')->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            if($request->has('contract_type_id') && $request->contract_type_id != "" && $filterFlag == true){
                $ids = SubcontractorStructure::whereIn('id',$ids)
                                    ->where('sc_structure_type_id', $request->contract_type_id)
                                    ->orderBy('created_at','desc')->pluck('id');
                if(count($ids) <= 0) {
                    $filterFlag = false;
                }
            }

            $listingData = array();
            if($request->has('project_name') || $request->has('subcontractor_name') || $request->has('contract_type_id')) {
                if ($filterFlag) {
                    $listingData = SubcontractorBillTransaction::join('subcontractor_bills','subcontractor_bill_transactions.subcontractor_bills_id','=','subcontractor_bills.id')
                                                                ->join('subcontractor_structure','subcontractor_bills.sc_structure_id','=','subcontractor_structure.id')
                                                                ->where('paid_from_slug', 'cash')
                                                                //->where('debit','<>', 0)
                                                                ->whereIn('subcontractor_structure.id',$ids)
                                                                ->with('subcontractorBill.subcontractorStructure','subcontractorBill.subcontractorStructure.projectSite.project','subcontractorBill.subcontractorStructure.subcontractor','subcontractorBill.subcontractorStructure.contractType')
                                                                ->orderBy('subcontractor_bill_transactions.id', 'desc')
                                                                ->skip($skip)->take($take)->get();
                    $totalRecordCount = SubcontractorBillTransaction::join('subcontractor_bills','subcontractor_bill_transactions.subcontractor_bills_id','=','subcontractor_bills.id')
                                        ->join('subcontractor_structure','subcontractor_bills.sc_structure_id','=','subcontractor_structure.id')->where('paid_from_slug', 'cash')->whereIn('subcontractor_structure.id',$ids)->where('debit','<>', 0)->count();
                }
            }else{
                $listingData = SubcontractorBillTransaction::where('paid_from_slug', 'cash')
                                                                //->where('debit','<>', 0)
                                                                ->with('subcontractorBill.subcontractorStructure','subcontractorBill.subcontractorStructure.projectSite.project','subcontractorBill.subcontractorStructure.subcontractor','subcontractorBill.subcontractorStructure.contractType')
                                                                ->orderBy('subcontractor_bill_transactions.id', 'desc')
                                                                ->skip($skip)->take($take)->get();
                $totalRecordCount = SubcontractorBillTransaction::where('paid_from_slug', 'cash')->count();
            }
            
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($listingData) : $request->length;
            for ($iterator = 0, $pagination = 0; $iterator < $end && $pagination < count($listingData); $iterator++, $pagination++) {
                $summaryArray = $listingData[$pagination]->subcontractorBill->subcontractorStructure->summaries;    
                $totalRate = array_sum(array_column($summaryArray->toArray(),'rate'));
                $totalWorkArea = array_sum(array_column($summaryArray->toArray(),'total_work_area'));;
                $totalAmount = $summaryArray->sum(function($summary){
                    return $summary->rate * $summary->total_work_area;
                });
                $cashTransactionCount = SubcontractorBillTransaction::where('subcontractor_bills_id',$listingData[$pagination]->subcontractorBill->id)->count();
                $action = '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">';
                if ($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-subcontractor-structure') || $user->customHasPermission('view-subcontractor-structure')) {
                    $action .= '<li>
                                    <a href="/subcontractor/structure/edit/'.$listingData[$pagination]->subcontractorBill->subcontractorStructure->id.'">
                                            <i class="icon-docs"></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);" onclick="getSummaries('
                                    ."'".$listingData[$pagination]->subcontractorBill->subcontractorStructure->id."',"
                                    ."'".$listingData[$pagination]->id."',"
                                    ."'".$listingData[$pagination]->total."',"
                                    ."'".$cashTransactionCount."')".'">
                                        <i class="icon-docs"></i>PriceEdit</a>
                                </li>';
                }
                if ($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('create-subcontractor-billing') || $user->customHasPermission('edit-subcontractor-billing') || $user->customHasPermission('view-subcontractor-billing') || $user->customHasPermission('approve-subcontractor-billing')) {
                    $action .= '<li><a href="/subcontractor/bill/manage/'.$listingData[$pagination]->subcontractorBill->subcontractorStructure->id.'">
                                        <i class="icon-docs"></i> Manage
                                    </a></li>';
                }
                $action .= '</ul>
                    </div>';
                
                

                $records['data'][$iterator] = [
                    ucwords($listingData[$pagination]->subcontractorBill->subcontractorStructure->subcontractor->company_name),
                    $listingData[$pagination]->subcontractorBill->subcontractorStructure->projectSite->project->name,
                    $listingData[$pagination]->subcontractorBill->subcontractorStructure->contractType->name,
                    $totalRate,
                    $totalWorkArea,
                    $totalAmount,
                    $listingData[$pagination]->total,
                    $listingData[$pagination]->is_modified,
                    date('d M Y', strtotime($listingData[$pagination]['modified_at'])),
                    date('d M Y', strtotime($listingData[$pagination]['created_at'])),
                    $action
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $totalRecordCount;
            $records["recordsFiltered"] = $totalRecordCount;
            $status = 200;
        }catch (\Exception $e){
            $records = [];
            $status = 500;
            $data = [
                'action' => 'subcontractor structure listing',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        return response()->json($records, $status);
    }

    public function cashEntryEdit(SubcontractorCashEditRequest $request, $id)
    {
        try{
            if($request->debit == 0) {
                $request->session()->flash('error', 'Amount cant be 0!');
                return redirect('/subcontractor/cashentry/manage');
            }
            $subcontractorBillTransaction = SubcontractorBillTransaction::where('id', $id)
            ->with('subcontractorBill.subcontractorStructure',
                   'subcontractorBill.subcontractorBillSummaries',
                   'subcontractorBill.subcontractorStructure.projectSite.project',
                   'subcontractorBill.subcontractorStructure.subcontractor',
                   'subcontractorBill.subcontractorStructure.contractType')->first();

            if(!is_null($subcontractorBillTransaction)) {

                $contractType = $subcontractorBillTransaction->subcontractorBill->subcontractorStructure->contractType->slug;
                $debit = $request->debit/$subcontractorBillTransaction->total;

                if($contractType == 'itemwise' || $contractType == 'sqft') {               
                    $subTotal = 0;
                    foreach($subcontractorBillTransaction->subcontractorBill->subcontractorBillSummaries as $billSummary) {
                        $quantity = $debit * $billSummary->quantity;
                        $billSummary->quantity = $quantity;
                        $billSummary->save();
                        $subTotal += $billSummary->subcontractorStructureSummary['rate']* $billSummary->quantity;
                    }
                }elseif($contractType == 'amountwise') {
                    $subTotal = 0;
                    foreach($subcontractorBillTransaction->subcontractorBill->subcontractorBillSummaries as $billSummary) {
                        $quantity = $debit * $billSummary->quantity;
                        $billSummary->quantity = $quantity;
                        $billSummary->save();
                        $subTotal += $billSummary['total_work_area'] * $billSummary->subcontractorStructureSummary['rate']* $billSummary->quantity;
                    }
                }
                $subcontractorBillTransaction->subcontractorBill->subtotal = $subTotal;
                $subcontractorBillTransaction->subcontractorBill->grand_total = $subTotal;
                $subcontractorBillTransaction->subcontractorBill->save(); 
                $subcontractorBillTransaction->total = $request->debit;
                $subcontractorBillTransaction->is_modified = true;
                $subcontractorBillTransaction->modified_at = Carbon::now();
                $subcontractorBillTransaction->modified_by = $request->user()->id;
                $subcontractorBillTransaction->save();
                $request->session()->flash('success', 'Amount updated successfully.');
            } else {
                $request->session()->flash('error', 'Record Not Found');
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'subcontractor cash edit',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $request->session()->flash('error', 'Something went wrong.');
        }
        return redirect('/subcontractor/cashentry/manage');
    }
}
