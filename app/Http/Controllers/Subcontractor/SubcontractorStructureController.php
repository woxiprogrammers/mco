<?php

namespace App\Http\Controllers\Subcontractor;

use App\ExtraItem;
use App\Project;
use App\Subcontractor;
use App\SubcontractorBill;
use App\SubcontractorBillStatus;
use App\SubcontractorBillSummary;
use App\SubcontractorBillTransaction;
use App\SubcontractorStructure;
use App\SubcontractorStructureExtraItem;
use App\SubcontractorStructureSummary;
use App\SubcontractorStructureType;
use App\Summary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            $subcontractors = Subcontractor::where('is_active',true)->orderBy('id','asc')->get(['id','subcontractor_name'])->toArray();
            $ScStrutureTypes = SubcontractorStructureType::orderBy('id','asc')->get(['id','name','slug'])->toArray();
            $summaries = Summary::where('is_active', true)->select('id', 'name')->get()->toArray();
            $extraItems = ExtraItem::where('is_active', true)->select('id','name','rate')->orderBy('name','asc')->get();
            return view('subcontractor.new_structure.create')->with(compact('subcontractors', 'ScStrutureTypes', 'summaries', 'extraItems'));
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
            foreach($request->summaries as $summaryId){
                $structureSummaryData['summary_id'] = (int) $summaryId;
                $structureSummaryData['rate'] = (float) $request->rate[$summaryId];
                $structureSummaryData['description'] = $request->description[$summaryId];
                $structureSummaryData['total_work_area'] = (float)$request->total_work_area[$summaryId];
                $structureSummary = SubcontractorStructureSummary::create($structureSummaryData);
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
            if ($filterFlag) {
                $listingData = SubcontractorStructure::whereIn('id',$ids)->orderBy('id', 'desc')->get();
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
                            $billPaidAmount += round((SubcontractorBillTransaction::where('subcontractor_bills_id',$subBillids)->sum('total')),3);
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
                for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($listingData); $iterator++, $pagination++) {
                    $subcontractorBillIds = $listingData[$pagination]->subcontractorBill->where('subcontractor_bill_status_id', SubcontractorBillStatus::where('slug', 'approved')->pluck('id')->first())->pluck('id');
                    $billTotals = 0;
                    $billPaidAmount = 0;
                    foreach ($subcontractorBillIds as $subcontractorStructureBillId) {
                        $subcontractorBill = SubcontractorBill::where('id', $subcontractorStructureBillId)->first();
                        $billTotals += round($subcontractorBill['grand_total'], 3);
                        $billPaidAmount += round((SubcontractorBillTransaction::where('subcontractor_bills_id', $subcontractorStructureBillId)->sum('total')), 3);
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
                $records["recordsTotal"] = $iTotalRecords;
                $records["recordsFiltered"] = $iTotalRecords;
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
            foreach($structureSummaries as $structureSummary){
                $structureSummaries[$iterator]['summary_name'] = Summary::where('id', $structureSummary['summary_id'])->pluck('name')->first();
                $subcontractorBillSummaries = SubcontractorBillSummary::where('subcontractor_structure_summary_id', $structureSummary['id'])->get();
                if ($subcontractorBillSummaries->isEmpty()){
                    $structureSummaries[$iterator]['min_rate'] = 1;
                    $structureSummaries[$iterator]['min_total_work_area'] = 1;
                    if ($subcontractorStructure->contractType->slug == 'itemwise'){
                        $structureSummaries[$iterator]['can_remove'] = true;
                    }else{
                        $structureSummaries[$iterator]['can_remove'] = false;
                    }
                } else {
                    $structureSummaries[$iterator]['min_rate'] = 1;
                    $structureSummaries[$iterator]['min_total_work_area'] = 1;
                    if ($subcontractorStructure->contractType->slug == 'itemwise'){
                        $structureSummaries[$iterator]['can_remove'] = true;
                    }else{
                        $structureSummaries[$iterator]['can_remove'] = false;
                    }
                  // Logic to restrict minimum rate and work area if bills and approved transactions are created.
                }
                $iterator += 1;
            }
            $structureExtraItemIds = array_column($subcontractorStructure->extraItems->toArray(), 'extra_item_id');
            $newExtraItems = ExtraItem::whereNotIn('id', $structureExtraItemIds)->where('is_active', true)->select('id','name','rate')->get()->toArray();
            return view('subcontractor.new_structure.edit')->with(compact('subcontractorStructure', 'summaries', 'structureSummaries', 'newExtraItems'));
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
            foreach ($request->summaries as $summaryId){
                $structureSummaryData = [
                    'subcontractor_structure_id' => $subcontractorStructure->id,
                    'summary_id' => $summaryId
                ];
                $subcontractorStructureSummary = SubcontractorStructureSummary::where($structureSummaryData)->first();
                $structureSummaryData['rate'] = (float) $request->rate[$summaryId];
                $structureSummaryData['description'] = $request->description[$summaryId];
                $structureSummaryData['total_work_area'] = (float)$request->total_work_area[$summaryId];
                if ($subcontractorStructureSummary == null){
                    $subcontractorStructureSummary = SubcontractorStructureSummary::create($structureSummaryData);
                }else{
                    $subcontractorStructureSummary->update($structureSummaryData);
                }
            }
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
}
